<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class AdminSystemController extends Controller
{
    public function index(): View
    {
        $modules = $this->modules();

        return view('admin.system.index', compact('modules'));
    }

    public function show(string $slug): View
    {
        $modules = $this->modules();
        $module = collect($modules)->firstWhere('slug', $slug);

        abort_unless($module !== null, 404);

        $comingSoonItems = [
            'This module UI is now live and linked from the management board.',
            'Backend settings persistence will be connected next.',
            'Validation, audit logs, and permission checks can be enabled per module.',
        ];

        $systemConfigurationItems = [
            ['title' => 'Online User Registration', 'description' => 'If this module is disabled, none can get registered on this system online.', 'enabled' => true],
            ['title' => 'Branch User Registration', 'description' => 'If this module is disabled, none can get registered from a branch.', 'enabled' => true],
            ['title' => 'Force SSL', 'description' => 'By enabling Force SSL, users must visit in secure mode.', 'enabled' => true],
            ['title' => 'Agree Policy', 'description' => 'Users must agree with your system policies during registration.', 'enabled' => true],
            ['title' => 'Force Secure Password', 'description' => 'Users must set strong passwords during signup and updates.', 'enabled' => false],
            ['title' => 'KYC Verification', 'description' => 'Users must submit required data before transaction features are enabled.', 'enabled' => false],
            ['title' => 'Email Verification', 'description' => 'Users verify email with a code before dashboard access.', 'enabled' => false],
            ['title' => 'Email Notification', 'description' => 'System sends emails to users where needed.', 'enabled' => true],
            ['title' => 'Mobile Verification', 'description' => 'Users verify mobile number before full access.', 'enabled' => false],
            ['title' => 'SMS Notification', 'description' => 'System sends SMS alerts where needed.', 'enabled' => false],
            ['title' => 'Language Option', 'description' => 'Users can change language according to their needs.', 'enabled' => true],
            ['title' => 'Deposit', 'description' => 'Enable or disable deposit operations.', 'enabled' => true],
            ['title' => 'Withdraw', 'description' => 'Enable or disable withdrawal operations.', 'enabled' => true],
            ['title' => 'FDR', 'description' => 'Enable or disable fixed deposit request module.', 'enabled' => true],
            ['title' => 'DPS', 'description' => 'Enable or disable deposit pension scheme module.', 'enabled' => true],
            ['title' => 'Loan', 'description' => 'Enable or disable loan request module.', 'enabled' => true],
            ['title' => 'Wallet', 'description' => 'Enable or disable wallet creation and usage.', 'enabled' => false],
            ['title' => 'Own Bank Transfer', 'description' => 'Allow transfer within internal bank accounts.', 'enabled' => true],
            ['title' => 'Other Bank Transfer', 'description' => 'Allow transfer to other local banks.', 'enabled' => true],
            ['title' => 'Wire Transfer', 'description' => 'Allow transfer to other banks or countries.', 'enabled' => true],
            ['title' => 'OTP Via Email', 'description' => 'Send OTP to user email when required.', 'enabled' => true],
            ['title' => 'OTP Via SMS', 'description' => 'Send OTP to user SMS when required.', 'enabled' => true],
            ['title' => 'Referral System', 'description' => 'Enable referral-based rewards and tracking.', 'enabled' => true],
            ['title' => 'Airtime', 'description' => 'Allow users recharge airtime from wallet balance.', 'enabled' => true],
            ['title' => 'Push Notification', 'description' => 'Send push notifications to users.', 'enabled' => true],
            ['title' => 'Auto Logout Idle Users', 'description' => 'Automatically logout inactive users after a period.', 'enabled' => false],
            ['title' => 'Automatic Currency Rate Update', 'description' => 'Auto-update exchange rates at intervals.', 'enabled' => false],
            ['title' => 'Account Level', 'description' => 'Assign users to levels based on activity.', 'enabled' => false],
            ['title' => 'Reward Point', 'description' => 'Allow reward point earning and redemption.', 'enabled' => false],
            ['title' => 'In App Payment', 'description' => 'Allow in-app payment integrations.', 'enabled' => true],
            ['title' => 'Virtual Card', 'description' => 'Enable or disable virtual card module.', 'enabled' => true],
            ['title' => 'Auto Activate Card', 'description' => 'Automatically activate newly issued cards.', 'enabled' => false],
        ];

        $moduleBlueprints = $this->moduleBlueprints();
        $savedSettings = $this->savedSettingsFor($slug);
        $generalSettingsFields = $this->generalSettingsFields();

        $systemConfigurationItems = array_map(function (array $item) use ($savedSettings): array {
            $key = Str::snake(Str::lower($item['title']));
            $savedValue = Arr::get($savedSettings, $key);

            if ($savedValue !== null) {
                $item['enabled'] = filter_var($savedValue, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? $item['enabled'];
            }

            $item['key'] = $key;

            return $item;
        }, $systemConfigurationItems);

        if (isset($moduleBlueprints[$slug])) {
            $moduleBlueprints[$slug]['fields'] = array_map(function (array $field) use ($savedSettings): array {
                if (isset($field['key']) && array_key_exists($field['key'], $savedSettings)) {
                    $field['value'] = $savedSettings[$field['key']];
                }

                return $field;
            }, $moduleBlueprints[$slug]['fields']);
        }

        $generalSettingsValues = [];
        foreach ($generalSettingsFields as $field) {
            $generalSettingsValues[$field['key']] = $savedSettings[$field['key']] ?? $field['value'];
        }

        return view('admin.system.show', [
            'module' => $module,
            'modules' => $modules,
            'comingSoonItems' => $comingSoonItems,
            'systemConfigurationItems' => $systemConfigurationItems,
            'moduleBlueprint' => $moduleBlueprints[$slug] ?? null,
            'generalSettingsFields' => $generalSettingsFields,
            'generalSettingsValues' => $generalSettingsValues,
        ]);
    }

    public function update(Request $request, string $slug): RedirectResponse
    {
        $modules = collect($this->modules());
        abort_unless($modules->pluck('slug')->contains($slug), 404);

        $allowedKeys = $this->allowedSettingKeysFor($slug);
        $inputSettings = $request->input('settings', []);
        $settings = [];

        if (is_array($inputSettings)) {
            foreach ($inputSettings as $key => $value) {
                if (!in_array($key, $allowedKeys, true)) {
                    continue;
                }

                if (is_array($value)) {
                    continue;
                }

                $settings[$key] = (string) $value;
            }
        }

        $this->upsertModuleSettings($slug, $settings);

        return redirect()
            ->route('admin.system.show', $slug)
            ->with('success', ucfirst(str_replace('-', ' ', $slug)) . ' settings updated.');
    }

    public function toggle(Request $request, string $slug): RedirectResponse
    {
        abort_unless($slug === 'system-configuration', 404);

        $validated = $request->validate([
            'key' => ['required', 'string'],
            'enabled' => ['required', 'boolean'],
        ]);

        $allowedKeys = collect($this->systemConfigurationDefinitions())
            ->map(fn (array $item) => Str::snake(Str::lower($item['title'])))
            ->all();

        abort_unless(in_array($validated['key'], $allowedKeys, true), 422);

        $this->upsertModuleSettings($slug, [
            $validated['key'] => $validated['enabled'] ? '1' : '0',
        ]);

        return redirect()
            ->route('admin.system.show', $slug)
            ->with('success', 'System configuration updated.');
    }

    private function modules(): array
    {
        $modules = [
            ['title' => 'General Settings', 'description' => 'Configure the fundamental information of the site.', 'icon' => '⚙'],
            ['title' => 'System Configuration', 'description' => 'Control all of the basic modules of the system.', 'icon' => '🧩'],
            ['title' => 'Logo and Favicon', 'description' => 'Upload your logo and favicon here.', 'icon' => '🖼'],
            ['title' => 'Notification Setting', 'description' => 'Control and configure overall notification elements of the system.', 'icon' => '🔔'],
            ['title' => 'SMTP Setup', 'description' => 'Configure SMTP credentials and delivery settings for outbound emails.', 'icon' => '✉'],
            ['title' => 'Payment Gateways', 'description' => 'Configure automatic or manual payment gateways to accept payment from users.', 'icon' => '💳'],
            ['title' => 'Withdrawals Methods', 'description' => 'Set up manual withdrawal method so users can make payout requests.', 'icon' => '🏦'],
            ['title' => 'Referral Setting', 'description' => 'Configure referral setting for user referral management.', 'icon' => '🌲'],
            ['title' => 'API Configuration', 'description' => 'Configure third party APIs from here.', 'icon' => '☁'],
            ['title' => 'Other Banks', 'description' => 'Manage all local bank from here.', 'icon' => '🏛'],
            ['title' => 'Wire Transfer', 'description' => 'Configure wire transfer setting and wire transfer form.', 'icon' => '🔀'],
            ['title' => 'Manage Branches', 'description' => 'Manage all branches and branch staff from here.', 'icon' => '🏢'],
            ['title' => 'Manage Plans', 'description' => 'Manage all plans of DPS, FDR, Loan from here.', 'icon' => '📊'],
            ['title' => 'Manage Airtime', 'description' => 'Configure airtime countries and operators from here.', 'icon' => '📱'],
            ['title' => 'KYC Setting', 'description' => 'Configure the dynamic input field to collect information of your client.', 'icon' => '🧬'],
            ['title' => 'Language', 'description' => 'Configure required languages and keywords to localize the system.', 'icon' => '🈯'],
            ['title' => 'Cron Job Setting', 'description' => 'Configure cron jobs to automate key operations of the system.', 'icon' => '⏰'],
            ['title' => 'Extensions', 'description' => 'Manage extensions of the system and add extra features.', 'icon' => '🧩'],
            ['title' => 'SEO Configuration', 'description' => 'Configure proper meta titles and description to make the system SEO-friendly.', 'icon' => '🌍'],
            ['title' => 'Manage Frontend', 'description' => 'Control all frontend contents of the system.', 'icon' => '🧱'],
            ['title' => 'Manage Pages', 'description' => 'Control dynamic and static pages of the system.', 'icon' => '📋'],
            ['title' => 'Manage Templates', 'description' => 'Manage templates from here.', 'icon' => '📐'],
        ];

        return array_map(static function (array $module): array {
            $title = Arr::get($module, 'title', 'Module');

            return [
                ...$module,
                'slug' => Str::slug($title),
            ];
        }, $modules);
    }

    private function moduleBlueprints(): array
    {
        return [
            'logo-and-favicon' => [
                'title' => 'Brand Assets',
                'description' => 'Control logos and favicon shown across app surfaces.',
                'fields' => [
                    ['key' => 'primary_logo_url', 'label' => 'Primary Logo URL', 'type' => 'text', 'value' => '/images/poise-logo.png'],
                    ['key' => 'favicon_url', 'label' => 'Favicon URL', 'type' => 'text', 'value' => '/favicon.ico'],
                    ['key' => 'email_header_logo_url', 'label' => 'Email Header Logo URL', 'type' => 'text', 'value' => '/images/email-logo.png'],
                    ['key' => 'brand_alt_text', 'label' => 'Brand Alt Text', 'type' => 'text', 'value' => 'Poise Commerce Bank'],
                ],
            ],
            'notification-setting' => [
                'title' => 'Notification Channels',
                'description' => 'Set default delivery channels and digest behavior.',
                'fields' => [
                    ['key' => 'default_sender_name', 'label' => 'Default Sender Name', 'type' => 'text', 'value' => 'Poise Alerts'],
                    ['key' => 'default_sender_email', 'label' => 'Default Sender Email', 'type' => 'text', 'value' => 'alerts@poisebank.com'],
                    ['key' => 'digest_frequency', 'label' => 'Digest Frequency', 'type' => 'select', 'options' => ['Instant', 'Hourly', 'Daily'], 'value' => 'Instant'],
                    ['key' => 'max_retries', 'label' => 'Max Retries', 'type' => 'number', 'value' => '3'],
                ],
            ],
            'smtp-setup' => [
                'title' => 'SMTP Setup',
                'description' => 'Manage SMTP transport details used for account alerts, OTPs, and transactional emails.',
                'fields' => [
                    ['key' => 'mailer', 'label' => 'Mailer', 'type' => 'select', 'options' => ['smtp', 'sendmail', 'mailgun'], 'value' => 'smtp'],
                    ['key' => 'smtp_host', 'label' => 'SMTP Host', 'type' => 'text', 'value' => 'smtp.mailtrap.io'],
                    ['key' => 'smtp_port', 'label' => 'SMTP Port', 'type' => 'number', 'value' => '587'],
                    ['key' => 'smtp_encryption', 'label' => 'Encryption', 'type' => 'select', 'options' => ['tls', 'ssl', 'none'], 'value' => 'tls'],
                    ['key' => 'smtp_username', 'label' => 'SMTP Username', 'type' => 'text', 'value' => ''],
                    ['key' => 'smtp_password', 'label' => 'SMTP Password', 'type' => 'text', 'value' => ''],
                    ['key' => 'mail_from_address', 'label' => 'From Address', 'type' => 'text', 'value' => 'noreply@poisebank.com'],
                    ['key' => 'mail_from_name', 'label' => 'From Name', 'type' => 'text', 'value' => 'Poise Commerce Bank'],
                ],
            ],
            'payment-gateways' => [
                'title' => 'Gateway Controls',
                'description' => 'Configure payment processing defaults and fallback logic.',
                'fields' => [
                    ['key' => 'default_gateway', 'label' => 'Default Gateway', 'type' => 'select', 'options' => ['Stripe', 'Paystack', 'Flutterwave'], 'value' => 'Stripe'],
                    ['key' => 'settlement_currency', 'label' => 'Settlement Currency', 'type' => 'text', 'value' => 'USD'],
                    ['key' => 'settlement_delay_hours', 'label' => 'Settlement Delay (Hours)', 'type' => 'number', 'value' => '24'],
                    ['key' => 'webhook_secret', 'label' => 'Webhook Secret', 'type' => 'text', 'value' => '****************'],
                ],
            ],
            'withdrawals-methods' => [
                'title' => 'Withdrawal Methods',
                'description' => 'Define limits, review behavior, and processing windows.',
                'fields' => [
                    ['key' => 'minimum_withdrawal', 'label' => 'Minimum Withdrawal', 'type' => 'number', 'value' => '50', 'suffix' => 'USD'],
                    ['key' => 'maximum_withdrawal', 'label' => 'Maximum Withdrawal', 'type' => 'number', 'value' => '25000', 'suffix' => 'USD'],
                    ['key' => 'manual_review_threshold', 'label' => 'Manual Review Threshold', 'type' => 'number', 'value' => '5000', 'suffix' => 'USD'],
                    ['key' => 'processing_window', 'label' => 'Processing Window', 'type' => 'select', 'options' => ['24/7', 'Business Hours', 'Custom'], 'value' => '24/7'],
                ],
            ],
            'referral-setting' => [
                'title' => 'Referral Rules',
                'description' => 'Control referral rewards, caps, and qualification.',
                'fields' => [
                    ['key' => 'referral_bonus', 'label' => 'Referral Bonus', 'type' => 'number', 'value' => '10', 'suffix' => 'USD'],
                    ['key' => 'minimum_first_deposit', 'label' => 'Minimum First Deposit', 'type' => 'number', 'value' => '100', 'suffix' => 'USD'],
                    ['key' => 'monthly_reward_cap', 'label' => 'Monthly Reward Cap', 'type' => 'number', 'value' => '1000', 'suffix' => 'USD'],
                    ['key' => 'expiry_period', 'label' => 'Expiry Period', 'type' => 'number', 'value' => '60', 'suffix' => 'Days'],
                ],
            ],
            'api-configuration' => [
                'title' => 'API Integrations',
                'description' => 'Manage API credentials, timeouts, and signing policies.',
                'fields' => [
                    ['key' => 'public_key', 'label' => 'Public Key', 'type' => 'text', 'value' => 'pk_live_xxxxxxxxxxxxx'],
                    ['key' => 'private_key', 'label' => 'Private Key', 'type' => 'text', 'value' => 'sk_live_xxxxxxxxxxxxx'],
                    ['key' => 'request_timeout', 'label' => 'Request Timeout', 'type' => 'number', 'value' => '30', 'suffix' => 'Seconds'],
                    ['key' => 'signature_algorithm', 'label' => 'Signature Algorithm', 'type' => 'select', 'options' => ['HMAC SHA256', 'RSA SHA256'], 'value' => 'HMAC SHA256'],
                ],
            ],
            'other-banks' => [
                'title' => 'External Bank Directory',
                'description' => 'Manage external banks used in transfer destinations.',
                'fields' => [
                    ['key' => 'default_country', 'label' => 'Default Country', 'type' => 'text', 'value' => 'United Kingdom'],
                    ['key' => 'swift_required', 'label' => 'SWIFT Required', 'type' => 'select', 'options' => ['Yes', 'No'], 'value' => 'Yes'],
                    ['key' => 'iban_validation', 'label' => 'IBAN Validation', 'type' => 'select', 'options' => ['Enabled', 'Disabled'], 'value' => 'Enabled'],
                    ['key' => 'max_destination_records', 'label' => 'Max Destination Records', 'type' => 'number', 'value' => '500'],
                ],
            ],
            'wire-transfer' => [
                'title' => 'Wire Transfer Settings',
                'description' => 'Configure wire processing, limits, and compliance metadata.',
                'fields' => [
                    ['key' => 'wire_fee', 'label' => 'Wire Fee', 'type' => 'number', 'value' => '35', 'suffix' => 'USD'],
                    ['key' => 'max_daily_wires', 'label' => 'Max Daily Wires', 'type' => 'number', 'value' => '5'],
                    ['key' => 'same_day_cutoff', 'label' => 'Same-Day Cutoff', 'type' => 'text', 'value' => '16:30'],
                    ['key' => 'compliance_review', 'label' => 'Compliance Review', 'type' => 'select', 'options' => ['Enabled', 'Disabled'], 'value' => 'Enabled'],
                ],
            ],
            'manage-branches' => [
                'title' => 'Branch Controls',
                'description' => 'Set branch onboarding defaults and staffing policies.',
                'fields' => [
                    ['key' => 'default_branch_status', 'label' => 'Default Branch Status', 'type' => 'select', 'options' => ['Active', 'Draft'], 'value' => 'Active'],
                    ['key' => 'branch_code_prefix', 'label' => 'Branch Code Prefix', 'type' => 'text', 'value' => 'PB'],
                    ['key' => 'max_staff_per_branch', 'label' => 'Max Staff per Branch', 'type' => 'number', 'value' => '100'],
                    ['key' => 'service_radius', 'label' => 'Service Radius', 'type' => 'number', 'value' => '25', 'suffix' => 'Miles'],
                ],
            ],
            'manage-plans' => [
                'title' => 'Plan Engine',
                'description' => 'Configure defaults for DPS, FDR, and loan plans.',
                'fields' => [
                    ['key' => 'default_plan_currency', 'label' => 'Default Plan Currency', 'type' => 'text', 'value' => 'USD'],
                    ['key' => 'min_interest_rate', 'label' => 'Min Interest Rate', 'type' => 'number', 'value' => '2.5', 'suffix' => '%'],
                    ['key' => 'max_interest_rate', 'label' => 'Max Interest Rate', 'type' => 'number', 'value' => '19.9', 'suffix' => '%'],
                    ['key' => 'auto_archive_inactive_plans', 'label' => 'Auto Archive Inactive Plans', 'type' => 'select', 'options' => ['Enabled', 'Disabled'], 'value' => 'Enabled'],
                ],
            ],
            'manage-airtime' => [
                'title' => 'Airtime Providers',
                'description' => 'Set top-up limits and provider routing options.',
                'fields' => [
                    ['key' => 'minimum_top_up', 'label' => 'Minimum Top-up', 'type' => 'number', 'value' => '1', 'suffix' => 'USD'],
                    ['key' => 'maximum_top_up', 'label' => 'Maximum Top-up', 'type' => 'number', 'value' => '500', 'suffix' => 'USD'],
                    ['key' => 'provider_mode', 'label' => 'Provider Mode', 'type' => 'select', 'options' => ['Auto Route', 'Manual Route'], 'value' => 'Auto Route'],
                    ['key' => 'retry_count', 'label' => 'Retry Count', 'type' => 'number', 'value' => '2'],
                ],
            ],
            'kyc-setting' => [
                'title' => 'KYC Pipeline',
                'description' => 'Control KYC levels, review SLA, and enforcement.',
                'fields' => [
                    ['key' => 'default_kyc_level', 'label' => 'Default KYC Level', 'type' => 'select', 'options' => ['Level 1', 'Level 2', 'Level 3'], 'value' => 'Level 1'],
                    ['key' => 'auto_approval_threshold', 'label' => 'Auto Approval Threshold', 'type' => 'number', 'value' => '70', 'suffix' => '%'],
                    ['key' => 'review_sla', 'label' => 'Review SLA', 'type' => 'number', 'value' => '24', 'suffix' => 'Hours'],
                    ['key' => 'required_document_count', 'label' => 'Required Document Count', 'type' => 'number', 'value' => '2'],
                ],
            ],
            'language' => [
                'title' => 'Localization',
                'description' => 'Set primary locale and fallback behavior.',
                'fields' => [
                    ['key' => 'default_language', 'label' => 'Default Language', 'type' => 'select', 'options' => ['English', 'French', 'Spanish'], 'value' => 'English'],
                    ['key' => 'fallback_language', 'label' => 'Fallback Language', 'type' => 'select', 'options' => ['English', 'French', 'Spanish'], 'value' => 'English'],
                    ['key' => 'rtl_support', 'label' => 'RTL Support', 'type' => 'select', 'options' => ['Enabled', 'Disabled'], 'value' => 'Disabled'],
                    ['key' => 'date_format', 'label' => 'Date Format', 'type' => 'text', 'value' => 'd M Y, H:i'],
                ],
            ],
            'cron-job-setting' => [
                'title' => 'Task Scheduling',
                'description' => 'Configure background jobs and execution windows.',
                'fields' => [
                    ['key' => 'cron_expression', 'label' => 'Cron Expression', 'type' => 'text', 'value' => '*/5 * * * *'],
                    ['key' => 'timezone', 'label' => 'Timezone', 'type' => 'text', 'value' => 'UTC'],
                    ['key' => 'max_runtime', 'label' => 'Max Runtime', 'type' => 'number', 'value' => '120', 'suffix' => 'Seconds'],
                    ['key' => 'missed_job_alert', 'label' => 'Missed Job Alert', 'type' => 'select', 'options' => ['Enabled', 'Disabled'], 'value' => 'Enabled'],
                ],
            ],
            'extensions' => [
                'title' => 'Extension Controls',
                'description' => 'Manage extension compatibility and loading order.',
                'fields' => [
                    ['key' => 'auto_update_extensions', 'label' => 'Auto Update Extensions', 'type' => 'select', 'options' => ['Enabled', 'Disabled'], 'value' => 'Disabled'],
                    ['key' => 'safe_mode', 'label' => 'Safe Mode', 'type' => 'select', 'options' => ['Enabled', 'Disabled'], 'value' => 'Enabled'],
                    ['key' => 'load_timeout', 'label' => 'Load Timeout', 'type' => 'number', 'value' => '20', 'suffix' => 'Seconds'],
                    ['key' => 'crash_isolation', 'label' => 'Crash Isolation', 'type' => 'select', 'options' => ['Enabled', 'Disabled'], 'value' => 'Enabled'],
                ],
            ],
            'seo-configuration' => [
                'title' => 'SEO Defaults',
                'description' => 'Configure metadata defaults and search crawling.',
                'fields' => [
                    ['key' => 'meta_title_template', 'label' => 'Meta Title Template', 'type' => 'text', 'value' => '{{page}} | Poise Commerce Bank'],
                    ['key' => 'meta_description', 'label' => 'Meta Description', 'type' => 'text', 'value' => 'Secure digital banking and payments platform.'],
                    ['key' => 'indexing', 'label' => 'Indexing', 'type' => 'select', 'options' => ['Allow', 'Disallow'], 'value' => 'Allow'],
                    ['key' => 'sitemap_interval', 'label' => 'Sitemap Interval', 'type' => 'select', 'options' => ['Daily', 'Weekly', 'Monthly'], 'value' => 'Weekly'],
                ],
            ],
            'manage-frontend' => [
                'title' => 'Frontend Controls',
                'description' => 'Set visual defaults, banners, and content flags.',
                'fields' => [
                    ['key' => 'theme_preset', 'label' => 'Theme Preset', 'type' => 'select', 'options' => ['Retail', 'Corporate', 'Minimal'], 'value' => 'Retail'],
                    ['key' => 'maintenance_banner', 'label' => 'Maintenance Banner', 'type' => 'select', 'options' => ['Enabled', 'Disabled'], 'value' => 'Disabled'],
                    ['key' => 'homepage_hero_variant', 'label' => 'Homepage Hero Variant', 'type' => 'select', 'options' => ['A', 'B', 'C'], 'value' => 'A'],
                    ['key' => 'public_cache_ttl', 'label' => 'Public Cache TTL', 'type' => 'number', 'value' => '10', 'suffix' => 'Minutes'],
                ],
            ],
            'manage-pages' => [
                'title' => 'Page Publishing',
                'description' => 'Control page drafts, publishing windows, and slugs.',
                'fields' => [
                    ['key' => 'default_page_status', 'label' => 'Default Page Status', 'type' => 'select', 'options' => ['Draft', 'Published'], 'value' => 'Draft'],
                    ['key' => 'auto_slug_generation', 'label' => 'Auto Slug Generation', 'type' => 'select', 'options' => ['Enabled', 'Disabled'], 'value' => 'Enabled'],
                    ['key' => 'revision_retention', 'label' => 'Revision Retention', 'type' => 'number', 'value' => '20'],
                    ['key' => 'scheduled_publish', 'label' => 'Scheduled Publish', 'type' => 'select', 'options' => ['Enabled', 'Disabled'], 'value' => 'Enabled'],
                ],
            ],
            'manage-templates' => [
                'title' => 'Template Library',
                'description' => 'Manage template defaults and active render engine.',
                'fields' => [
                    ['key' => 'default_template', 'label' => 'Default Template', 'type' => 'text', 'value' => 'modern-bank'],
                    ['key' => 'template_version', 'label' => 'Template Version', 'type' => 'text', 'value' => 'v2.4.1'],
                    ['key' => 'render_engine', 'label' => 'Render Engine', 'type' => 'select', 'options' => ['Blade', 'Hybrid'], 'value' => 'Blade'],
                    ['key' => 'strict_mode', 'label' => 'Strict Mode', 'type' => 'select', 'options' => ['Enabled', 'Disabled'], 'value' => 'Enabled'],
                ],
            ],
        ];
    }

    private function generalSettingsFields(): array
    {
        return [
            ['key' => 'site_title', 'label' => 'Site Title', 'type' => 'text', 'value' => 'Poise Commerce Bank'],
            ['key' => 'currency', 'label' => 'Currency', 'type' => 'text', 'value' => 'USD'],
            ['key' => 'currency_symbol', 'label' => 'Currency Symbol', 'type' => 'text', 'value' => '$'],
            ['key' => 'timezone', 'label' => 'Timezone', 'type' => 'select', 'options' => ['UTC', 'Europe/London', 'America/New_York'], 'value' => 'UTC'],
            ['key' => 'site_base_color', 'label' => 'Site Base Color', 'type' => 'color', 'value' => '00A6F7'],
            ['key' => 'site_secondary_color', 'label' => 'Site Secondary Color', 'type' => 'color', 'value' => '14233D'],
            ['key' => 'record_per_page', 'label' => 'Record to Display Per page', 'type' => 'select', 'options' => ['15 items per page', '25 items per page', '50 items per page'], 'value' => '15 items per page'],
            ['key' => 'currency_showing_format', 'label' => 'Currency Showing Format', 'type' => 'select', 'options' => ['Show Currency Symbol Only', 'Show Currency Code Only', 'Show Symbol and Code'], 'value' => 'Show Currency Symbol Only'],
            ['key' => 'account_number_prefix', 'label' => 'Account Number Prefix', 'type' => 'text', 'value' => 'VB'],
            ['key' => 'account_number_length', 'label' => 'Account Number Length', 'type' => 'number', 'value' => '15'],
            ['key' => 'otp_expiration_time', 'label' => 'OTP Expiration Time', 'type' => 'number', 'value' => '300', 'suffix' => 'Seconds'],
            ['key' => 'user_idle_time', 'label' => 'User Idle Time', 'type' => 'number', 'value' => '180', 'suffix' => 'Seconds'],
            ['key' => 'statement_fee', 'label' => 'Statement Fee', 'type' => 'number', 'value' => '0.00', 'suffix' => 'USD'],
        ];
    }

    private function systemConfigurationDefinitions(): array
    {
        return [
            ['title' => 'Online User Registration'],
            ['title' => 'Branch User Registration'],
            ['title' => 'Force SSL'],
            ['title' => 'Agree Policy'],
            ['title' => 'Force Secure Password'],
            ['title' => 'KYC Verification'],
            ['title' => 'Email Verification'],
            ['title' => 'Email Notification'],
            ['title' => 'Mobile Verification'],
            ['title' => 'SMS Notification'],
            ['title' => 'Language Option'],
            ['title' => 'Deposit'],
            ['title' => 'Withdraw'],
            ['title' => 'FDR'],
            ['title' => 'DPS'],
            ['title' => 'Loan'],
            ['title' => 'Wallet'],
            ['title' => 'Own Bank Transfer'],
            ['title' => 'Other Bank Transfer'],
            ['title' => 'Wire Transfer'],
            ['title' => 'OTP Via Email'],
            ['title' => 'OTP Via SMS'],
            ['title' => 'Referral System'],
            ['title' => 'Airtime'],
            ['title' => 'Push Notification'],
            ['title' => 'Auto Logout Idle Users'],
            ['title' => 'Automatic Currency Rate Update'],
            ['title' => 'Account Level'],
            ['title' => 'Reward Point'],
            ['title' => 'In App Payment'],
            ['title' => 'Virtual Card'],
            ['title' => 'Auto Activate Card'],
        ];
    }

    private function allowedSettingKeysFor(string $slug): array
    {
        if ($slug === 'general-settings') {
            return collect($this->generalSettingsFields())->pluck('key')->all();
        }

        if ($slug === 'system-configuration') {
            return collect($this->systemConfigurationDefinitions())
                ->map(fn (array $item) => Str::snake(Str::lower($item['title'])))
                ->all();
        }

        $blueprint = $this->moduleBlueprints()[$slug] ?? null;
        if (!$blueprint) {
            return [];
        }

        return collect($blueprint['fields'])->pluck('key')->filter()->values()->all();
    }

    private function savedSettingsFor(string $slug): array
    {
        return SystemSetting::query()
            ->where('module', $slug)
            ->pluck('value', 'key')
            ->toArray();
    }

    private function upsertModuleSettings(string $slug, array $settings): void
    {
        if ($settings === []) {
            return;
        }

        $rows = [];
        foreach ($settings as $key => $value) {
            $rows[] = [
                'module' => $slug,
                'key' => $key,
                'value' => (string) $value,
                'updated_at' => now(),
                'created_at' => now(),
            ];
        }

        SystemSetting::query()->upsert(
            $rows,
            ['module', 'key'],
            ['value', 'updated_at']
        );
    }
}
