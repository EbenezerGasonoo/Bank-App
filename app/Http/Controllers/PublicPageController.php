<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PublicPageController extends Controller
{
    /**
     * @return array<string, array{
     *     title: string,
     *     headline: string,
     *     intro: string,
     *     sections: array<int, array{heading: string, paragraphs: string[], list?: string[], callout?: string}>,
     *     disclaimer?: string
     * }>
     */
    private static function content(): array
    {
        return [
            'personal' => [
                'title' => 'Personal banking',
                'headline' => 'Banking that respects your time, your money, and your peace of mind',
                'intro' => 'Whether you are opening your first account, consolidating your finances, or planning for a major life goal, Poise Commerce Bank gives you a clear, secure digital experience backed by people who can help when the numbers do not tell the whole story. Our personal proposition is simple: strong foundations for everyday spending and saving, with transparent fees and tools that make it easy to see where you stand.',
                'sections' => [
                    [
                        'heading' => 'Current accounts',
                        'paragraphs' => [
                            'A Poise current account is built for the rhythm of real life: salary in, bills out, contactless on the go, and real-time balance visibility from any device. You can set up payees, schedule payments, and review transaction history with references that make reconciliation easy when you are budgeting at month end.',
                            'We focus on plain English in statements and in-app messages. Where fees apply — for example, certain international item charges or out-of-network ATM use in future network programmes — you will see them in our tariff guide before you confirm, not as a surprise on a statement two weeks later.',
                        ],
                        'list' => [
                            'GBP as your primary operating currency, with a simple fee schedule',
                            'Debit card controls, including quick freeze, where product features are enabled for your profile',
                            'Access to our transfer journey with email one-time passcodes for certain payments, for an extra check before money leaves your account',
                        ],
                    ],
                    [
                        'heading' => 'Savings and building habits',
                        'paragraphs' => [
                            'Saving is not only about the rate — it is about consistency. We help you split goals (emergency, holiday, home) so you can name what you are working toward. Instant-access and fixed-term options are available subject to product rules and your eligibility when you apply.',
                        ],
                    ],
                    [
                        'heading' => 'Digital first, with human support',
                        'paragraphs' => [
                            'You can do most of what you need in online banking. When something is urgent — a card stuck abroad, a payment that does not look right, or a bereavement that affects the estate — we maintain clear routes to a trained colleague who can see your relationship in context, not as a single ticket in a queue.',
                        ],
                    ],
                    [
                        'heading' => 'What we ask of you',
                        'paragraphs' => [
                            'In line with regulatory requirements, we verify identity and address when you join us, and we may ask for updated evidence from time to time. This protects every customer on the platform and helps us fight financial crime. We keep requests proportionate and explain why we are asking.',
                        ],
                        'callout' => 'Lending, overdrafts, and certain investment services are subject to status, our credit policy, and separate terms. Nothing on this page is a commitment to provide credit.',
                    ],
                ],
                'disclaimer' => 'Services and features vary by customer segment, jurisdiction, and product. Fees, interest, and foreign exchange conditions are as published in our product literature at the time you take out or renew a product.',
            ],

            'savings' => [
                'title' => 'Savings & certificates of deposit',
                'headline' => 'Grow balances with clear terms and a name for every goal',
                'intro' => 'A savings relationship is not one-size-fits-all. Some customers want instant access and flexibility. Others are prepared to lock funds away for a period in exchange for a known return on the agreed terms. Poise Commerce Bank structures savings products so you can align structure to intention — and always with clarity on how interest is calculated, when it is paid, and what happens if you need access early.',
                'sections' => [
                    [
                        'heading' => 'Instant-access savings',
                        'paragraphs' => [
                            'Ideal for rainy-day funds, short-horizon projects, and topping up without locking money away. Interest is calculated on the daily cleared balance in line with the product’s published basis and credited according to the schedule in your terms. Rates for new and existing business may differ when our published rates change; we communicate material changes in line with regulatory and contractual requirements.',
                        ],
                    ],
                    [
                        'heading' => 'Term deposits and CDs',
                        'paragraphs' => [
                            'When you choose a fixed term, you are committing the principal for the agreed length at the stated rate or rate formula, subject to the product documentation. Early withdrawal may be restricted or may incur a penalty; that will always be set out before you place the deposit, so you can make an informed trade-off between return and access.',
                        ],
                        'list' => [
                            'Competitive options for 3, 6, 12, and 24+ month terms where we offer them',
                            'Clear schedule for interest payment or compounding, per product',
                            'Statements that show your maturity date and the options you have on expiry',
                        ],
                    ],
                    [
                        'heading' => 'FSCS and protection',
                        'paragraphs' => [
                            'Eligible deposits with UK banks may be protected by the Financial Services Compensation Scheme up to the applicable limit. We will confirm your product’s status in the key facts you receive. Investments and other non-deposit products are not covered the same way; where we offer them, you will see separate risk disclosures.',
                        ],
                        'callout' => 'The value of investments can go up or down. Past performance is not a guide to the future. This is general information, not personal advice.',
                    ],
                    [
                        'heading' => 'Getting started',
                        'paragraphs' => [
                            'You can explore rates and start an application from the journey linked from our website or, once you are a customer, from the secure area. We will ask for identity and address verification, source of funds where required, and tax information where the law says we must. Decisions and documentation are available securely online where possible, with paper for those who need it.',
                        ],
                    ],
                ],
                'disclaimer' => 'Interest rates are variable or fixed as stated per product. Gross and AER figures are published for comparison. Tax treatment depends on your circumstances and may change.',
            ],

            'fdr' => [
                'title' => 'Fixed Deposit Receipt (FDR)',
                'headline' => 'Lock funds, earn predictable returns, and plan maturity with confidence',
                'intro' => 'Poise Commerce Bank Fixed Deposit Receipt (FDR) products are designed for customers who prefer known maturity outcomes over variable-rate uncertainty. Choose a tenure, lock your principal for the agreed term, and receive returns according to your selected payout style (maturity or periodic).',
                'sections' => [
                    [
                        'heading' => 'How FDR works',
                        'paragraphs' => [
                            'An FDR places your principal on a fixed tenure at a fixed rate stated in your product summary. During the term, withdrawals are restricted and may attract an early break penalty where permitted by terms.',
                            'At maturity, you can choose to credit proceeds to your current account, renew for another term at the prevailing rate, or set maturity instructions in advance.',
                        ],
                        'list' => [
                            'Indicative tenures: 3, 6, 12, and 24 months',
                            'Minimum placement amounts vary by customer segment',
                            'Clear maturity instruction options before expiry',
                        ],
                    ],
                    [
                        'heading' => 'Who should use FDR',
                        'paragraphs' => [
                            'FDR is often suitable for customers with near-term goals and low risk appetite for principal fluctuation. It is not designed for funds you may need immediately.',
                        ],
                        'callout' => 'Early withdrawal may reduce or forfeit expected interest depending on the product terms in force at placement.',
                    ],
                ],
                'disclaimer' => 'Rates shown in calculators are illustrative. Actual rates, eligibility, and terms are confirmed at account opening or renewal.',
            ],

            'business' => [
                'title' => 'Business banking',
                'headline' => 'From first invoice to full treasury — a partner for every stage of growth',
                'intro' => 'Small and medium businesses are the engine of the economy, but the banking they need is rarely a single product. It is cash-flow visibility, fast payments, clear pricing for day-to-day activity, and a relationship that scales when you add staff, locations, or cross-border trade. Poise Commerce Bank’s business banking proposition is designed around that reality.',
                'sections' => [
                    [
                        'heading' => 'Operating accounts and payment flows',
                        'paragraphs' => [
                            'A business current account is the control tower for incomings and outgoings. We support you with online access for you and, where you configure them, your finance team, with the ability to set permissions and to audit who approved what, when. Domestic payments, standing orders, and (where you qualify) same-day and international options can sit on a coherent fee and limit structure, explained up front in our business tariff and agreements.',
                        ],
                    ],
                    [
                        'heading' => 'Card programmes',
                        'paragraphs' => [
                            'From employee expense cards to company debit, we can structure card use around your policy: limits, merchant categories, and the ability to freeze a card the moment a device is lost. Reporting integrates with the activity you need for VAT, reimbursement, and board oversight.',
                        ],
                    ],
                    [
                        'heading' => 'Credit and overdrafts',
                        'paragraphs' => [
                            'Working capital needs vary by sector and season. We assess commercial lending, overdrafts, and asset-backed options against your accounts, your trading history, and the security we can take. We explain pricing, covenants, and monitoring clearly — you should always know what could trigger a review, not discover it in a default letter without context.',
                        ],
                    ],
                    [
                        'heading' => 'Onboarding and ongoing care',
                        'paragraphs' => [
                            'We are required to understand the nature of your business, your ownership structure, and, for higher-risk cases, the source of funds you move through the bank. That is not bureaucracy for its own sake; it is how we keep the system safe. A dedicated business manager may be available depending on your segment, scale, and complexity of needs.',
                        ],
                    ],
                ],
                'disclaimer' => 'Business services are subject to application, our commercial credit policy, and our acceptance criteria. All lending is subject to status and our assessment of your ability to repay. Fees and product availability vary by segment.',
            ],

            'commercial' => [
                'title' => 'Commercial & institutional',
                'headline' => 'Deeper balance sheets, larger flows, dedicated relationship coverage',
                'intro' => 'Larger corporate and public-sector type clients have requirements that go beyond a standard business package: notional pools, more sophisticated payment and collection structures, and conversations that connect treasury, tax, and strategic finance. The Poise commercial desk works with a smaller number of more complex clients and builds multi-year roadmaps, not one-off product sales.',
                'sections' => [
                    [
                        'heading' => 'Relationship model',
                        'paragraphs' => [
                            'We assign a named team where scale justifies it: relationship coverage, product specialists, and, where you need them, sector analysts who understand regulatory drivers in your industry. Governance is two-way: we expect you to run transparent reporting, and in return you get a counterparty that can respond at pace when markets or your board shift priorities.',
                        ],
                    ],
                    [
                        'heading' => 'Liquidity, deposits, and liability management',
                        'paragraphs' => [
                            'We can structure call and term money, certificate programmes, and — where we offer it — more tailored liability products within our risk appetite. All structures are subject to our internal credit and liquidity policy, to regulatory limits, and to the documentation you and we sign. Nothing in marketing material is an offer; offers are only made in a formal terms sheet and facility letter.',
                        ],
                    ],
                    [
                        'heading' => 'Non-retail products',
                        'paragraphs' => [
                            'For institutional investors and sophisticated counterparties, the suite may include access to our capital markets distribution and investment-bank-style product (where we provide it) under a separate contract and regulatory assessment. We treat suitability and appropriateness as non-negotiable, especially where you act on your own account or for third-party asset owners.',
                        ],
                        'callout' => 'This material is for information only, not a solicitation to buy a financial product in any jurisdiction. Professional advice should be taken before entering into a commitment.',
                    ],
                ],
                'disclaimer' => 'Commercial and institutional services are not available in all geographies. Eligibility, documentation, and pricing are determined through our formal on-boarding process.',
            ],

            'customer-service' => [
                'title' => 'Customer service',
                'headline' => 'Ways to reach us, what to have ready, and what we can resolve on the spot',
                'intro' => 'Good service is not only a friendly voice — it is the right answer the first time, a secure channel for sensitive data, and fair process when something goes wrong. This page sets out the main contact paths for Poise Commerce Bank, typical response times, and the information that helps us help you without unnecessary back-and-forth.',
                'sections' => [
                    [
                        'heading' => 'How to get in touch',
                        'paragraphs' => [
                            'Secure online banking: best for most account servicing, card controls, and payment queries where you are already signed in. Telephone: for urgent matters, lost or stolen cards, or when you cannot access the app. Email and web form: for general enquiries that do not include your full account number or passcodes. We will never ask you to disclose your full online password or a one-time code by email. If someone does, it is a scam: hang up, call the number on our site.',
                        ],
                        'list' => [
                            'UK support line: 0800 000 0000 (example — use our published number on letters and the website header)',
                            'Deaf and hard-of-hearing: contact options including relay services where we provide them',
                            'Written complaints: follow the process in the complaints section; we have regulatory timelines to acknowledge and resolve',
                        ],
                    ],
                    [
                        'heading' => 'Before you call',
                        'paragraphs' => [
                            'Please have to hand: your name as on the account, the last part of the account or card (never send full details by unsecured email), and a clear description of the transaction or issue, including the date, amount, and any reference. For disputes, we may need you to complete a form so that we can charge back or trace a payment in line with scheme rules.',
                        ],
                    ],
                    [
                        'heading' => 'Vulnerable customers and extra support',
                        'paragraphs' => [
                            'We train colleagues to identify circumstances where a customer may need a bit more time, a different format, or specialist referral — for example, temporary illness, loss of a partner, or difficulty using digital channels. Tell us if there is a way of communicating that works better for you, and we will do what we can within operational reality.',
                        ],
                    ],
                ],
            ],

            'security-center' => [
                'title' => 'Security center',
                'headline' => 'How we protect you — and the habits that keep fraud at bay',
                'intro' => 'We invest continuously in our systems: encryption, monitoring, and resilience. Security is also a partnership: the strongest bank controls in the world cannot stop a customer from handing a one-time passcode to a criminal who has engineered trust. The pages below set out the technical measures we use, the behaviours we expect, and what to do the moment you suspect a problem.',
                'sections' => [
                    [
                        'heading' => 'Technology and process',
                        'paragraphs' => [
                            'We use industry-standard transport security for sessions between your device and our services. We monitor for unusual login and payment patterns and may block or place additional checks on activity that is out of line with your profile, including step-up authentication when the risk score rises.',
                            'For payments to a third party’s account number, we have implemented a strong customer authentication path that includes a one-time code to your registered email, so a stolen device alone is not always enough to complete a new payee transfer.',
                        ],
                    ],
                    [
                        'heading' => 'You can take action, too',
                        'paragraphs' => [
                            'Use unique and strong passwords for your email and banking, and do not reuse them. Turn on two-factor authentication where we offer it. Be sceptical of urgency: we will not cold-call to move money to a “safe account” — that is always a fraud pattern. If you are unsure, end the call and call us on the public number. Keep your software updated and avoid banking on public WiFi without a VPN for sensitive work.',
                        ],
                    ],
                    [
                        'heading' => 'If something looks wrong',
                        'paragraphs' => [
                            'If you see a payment you do not recognise, a login from an unknown place, or you think someone has your details, contact us immediately. We can block cards, reset credentials, and guide you on reporting to Action Fraud in the UK where that is appropriate. Quick reporting materially improves the chance of recovery, though it is never guaranteed.',
                        ],
                        'callout' => 'Criminals evolve tactics constantly. The principles stay the same: we will never ask for your full password or a one-time code to “validate” a call, and we will not ask you to move money to “protect” it.',
                    ],
                ],
            ],

            'atms-and-branches' => [
                'title' => 'ATMs, branches, and the Poise service network',
                'headline' => 'When you need a face, a line, or cash — and when digital is the better path',
                'intro' => 'We are a digitally led bank, but we recognise that not every need fits an app. Some people prefer to discuss a life event in a private room, not a web chat. Some businesses still need a certified true copy or a high-value handoff at an agreed time. The following sections describe the physical and partner channels we are building, how hours work, and how to get help on the ground.',
                'sections' => [
                    [
                        'heading' => 'Branch coverage',
                        'paragraphs' => [
                            'We are growing a small network of high-service branches in key commercial centres, focused on relationship banking, new account opening, and in-person support for more complex needs. Branches that we operate directly offer scheduled appointments, drop-in for everyday queries where we have capacity, and self-service kiosks for the transactions that do not need a teller. Opening hours and holiday closures are published on our site and at each location.',
                        ],
                    ],
                    [
                        'heading' => 'ATMs and cash access',
                        'paragraphs' => [
                            'We participate in a shared ATM network so you can access cash in city centres, transport hubs, and retail areas. Surcharge-free and fee-ATM rules depend on the machine operator and the terms of your product; your tariff summary explains what we will charge when you use a fee-charging network device. In future, we will publish a live ATM finder with filters for accessibility, deposit capability, and foreign currency in selected markets.',
                        ],
                    ],
                    [
                        'heading' => 'Not near a branch? You still have a bank',
                        'paragraphs' => [
                            'Most servicing is online, by phone, or (where you qualify) with a visit from a private banker. For documents that must be notarised or require original ID, we work with a panel of vetted third-party service points or partner with courier services for secure return of material when law allows. Ask customer service for the option that best fits your situation and region.',
                        ],
                    ],
                ],
            ],

            'loans' => [
                'title' => 'Loans & credit',
                'headline' => 'Borrowing with a clear offer, a fair assessment, and no small print in six-point type',
                'intro' => 'We lend where we can do so prudently: when we understand your income and outgo, your purpose, and the security or guarantee that supports the facility. The journey below is typical for a personal or small-business applicant; commercial and property lending follow separate documentation, but the same values apply: transparency, a single all-in view of the cost, and a decision that is explained, not just delivered as “approved” or “declined”.',
                'sections' => [
                    [
                        'heading' => 'What we can finance',
                        'paragraphs' => [
                            'Depending on your profile, we may offer personal loans, overdrafts, and hire-purchase or lease options on vehicles and equipment, as well as structured business facilities. Property lending is a specialist activity with its own LTV, stress-test, and valuation process; for regulated mortgage credit, you will receive tailored disclosures, including a European Standardised Information Sheet (ESIS) in the EEA/UK where applicable, before you are bound.',
                        ],
                    ],
                    [
                        'heading' => 'How we decide',
                        'paragraphs' => [
                            'We use credit bureaux and internal models, and we also look at the relationship: conduct on your current account, stability of income, and, for businesses, filed accounts, management information, and sector context. A human underwriter is involved in borderline or higher-value cases, not for box-ticking but to apply judgment where a model is uncertain. You have the right to a clear explanation in line with our regulatory obligations where a decision is automated or has significant impact.',
                        ],
                    ],
                    [
                        'heading' => 'Rates, fees, and the total you repay',
                        'paragraphs' => [
                            'We quote an Annual Percentage Rate (or equivalent) that reflects the interest, mandatory fees, and, where we must include it, the cost of ancillary products you must take. Optional insurance is not bundled into a headline “rate” in a way that confuses. If the rate is variable, we will explain what index or internal reference drives a change, and the notice you receive before a payment shifts materially.',
                        ],
                    ],
                ],
                'disclaimer' => 'Credit is subject to status. Over 18s only, UK (or as stated) residents. Missed or late payments may impact your credit file. Your home or other property may be repossessed if you do not keep up repayments on a mortgage or other loan secured on it. Nothing here is a mortgage / loan offer; seek independent financial advice for major commitments.',
            ],

            'wealth' => [
                'title' => 'Wealth, planning & investments',
                'headline' => 'Building plans that outlast a single year’s return',
                'intro' => 'Wealth is the net of your assets and liabilities, but good wealth management is about the life you are funding, not a league table. Where we are authorised to provide investment and advisory services, we begin with a fact find on goals, time horizon, risk tolerance, and tax context. Only then do we talk about products — because the right product for you is a consequence of the plan, not a commission-driven starting point. Where we do not offer advice, we are clear, and we signpost to regulated third parties or execution-only services as appropriate.',
                'sections' => [
                    [
                        'heading' => 'Planning, not just products',
                        'paragraphs' => [
                            'Retirement, education, succession, and philanthropy each have their own time scales and tax wrinkles. A Poise private banker (where you qualify) can work with you and, where you appoint them, with your tax and legal counsel to co-ordinate banking, liquidity, and investment instructions. The aim is a net outcome after tax, fees, and inflation, not a brochure yield.',
                        ],
                    ],
                    [
                        'heading' => 'Investments and risk',
                        'paragraphs' => [
                            'Where we offer portfolio management or self-directed access to market instruments, you will see risk ratings, target volatility bands, and plain-language description of the worst plausibly bad year we can model, not a disclaimer only. Past performance is not a guide. Capital is at risk; you may get back less than you put in, especially over short horizons. Insurance-based investments may include provider charges and a surrender value that is not guaranteed.',
                        ],
                        'list' => [
                            'Execution-only, advisory, and discretionary service tiers where we offer them — different fees, different duty of care',
                            'Diversification across geographies, sectors, and asset types where agreed',
                            'Regular review meetings, at a cadence that matches the complexity of your case',
                        ],
                    ],
                ],
                'disclaimer' => 'Investment, pension, and insurance services are not available to all customers and may be provided by a Poise group company or a partner, subject to separate terms. Not all products are FSCS-protected; we will make that explicit before you sign.',
            ],

            'international' => [
                'title' => 'International payments & FX',
                'headline' => 'When life or business spans borders, the wiring should be as clear as a domestic credit',
                'intro' => 'Whether you are paying an overseas supplier, repatriating income, or supporting family abroad, you need a transparent chain: what we charge, when the money leaves your account, what rate applies, and what cut-off time applies in London for same-day or next-day value in the destination. Our international service is designed around that transparency, and around compliance: sanctions screening, beneficiary name checks, and, where the amount or corridor needs it, an extra look before value date.',
                'sections' => [
                    [
                        'heading' => 'Currencies, corridors, and value dates',
                        'paragraphs' => [
                            'GBP is the core currency of our retail relationship in the United Kingdom, but you can order payments in a wide set of other currencies and benefit from a spot or forward rate process where you qualify, depending on the product. Value dates and SWIFT/SEPA/RTGS routing depend on the target country’s clearing rules; we will give you a commitment on when the debit to your account occurs and, where possible, when the beneficiary can expect the credit, subject to intermediary banks in the path.',
                        ],
                    ],
                    [
                        'heading' => 'FX rate and cost',
                        'paragraphs' => [
                            'You may see a reference rate (e.g. mid-market) and a spread, or a single all-in client rate, depending on the product. We are moving toward clearer disclosure of that spread so you can compare providers meaningfully, within the market structure we are given. Third-party and correspondent-bank fees may be deducted in transit, especially for exotics; the beneficiary can receive a lower amount or you may be offered an option to pay those charges in advance, depending on the instruction type.',
                        ],
                    ],
                    [
                        'heading' => 'Compliance and you',
                        'paragraphs' => [
                            'We must comply with international sanctions, anti-money-laundering rules, and, in some cases, capital controls. That may mean a delay, a request for the source of a transfer, or a hard decline where the instruction cannot lawfully be made. It is not personal. We will, where the law allows, give you a reason in generic terms, and a route to re-submit if the matter can be fixed.',
                        ],
                    ],
                ],
            ],

            'support' => [
                'title' => 'Help, guides & support',
                'headline' => 'Find the right help whether you are setting up, stuck, or disputing a charge',
                'intro' => 'This help hub is the home for walkthroughs, what to do when something goes wrong, and the definitions that make a statement line intelligible. If you are already a customer, sign in to see the status of your cases and the secure messages we have sent you. If you are not, you can still use public guides, find contact information, and understand the complaints process the regulator expects us to run fairly.',
                'sections' => [
                    [
                        'heading' => 'New to Poise',
                        'paragraphs' => [
                            'Start with opening an account online: you will need identity and address evidence, a mobile and email, and, for some products, a minimum funding amount. The journey tells you as you go. Once live, we recommend you verify your contact details, turn on the strongest sign-in you can, and set up a few trusted payees before you are in a hurry. Our guides also cover what “pending”, “settled”, and “returned” mean in payments.',
                        ],
                    ],
                    [
                        'heading' => 'When something is wrong',
                        'paragraphs' => [
                            'A duplicate charge, a missing refund, a payment that is stuck, or a login you did not do — the first step is usually secure chat or a phone call so we can secure the account, trace the payment, and, where a chargeback is possible, start the clock under card scheme or bank rules. Keep any retailer emails or order references; they speed the investigation.',
                        ],
                    ],
                    [
                        'heading' => 'Complaints and the Financial Ombudsman Service',
                        'paragraphs' => [
                            'We hope you will not need this often. If you are not satisfied with our final response, or we have not responded in time, you may be able to refer the matter to the Financial Ombudsman Service, free to consumers, and to many small businesses, subject to the rules in force when you bring the case. We will give you a leaflet in our final response letter, or you can find the FOS online.',
                        ],
                    ],
                ],
            ],

            'faq' => [
                'title' => 'Frequently asked questions',
                'headline' => 'Straight answers to the questions we hear most often',
                'intro' => 'Below is a non-exhaustive set of common questions. Your specific terms are always in the agreement and tariff you accepted when you opened or renewed a product. If there is a conflict, the agreement wins — but in practice we aim for these answers to be consistent with our current practice. For regulated advice, speak to a professional.',
                'sections' => [
                    [
                        'heading' => 'Access and sign-in',
                        'paragraphs' => [
                            'Q: I forgot my password. A: Use “Forgot password” on the sign-in page. We will only send a reset to your registered email. The link is short-lived. If you have lost access to that email, you must re-verify identity through our recovery process, which is more manual by design, to protect you from an attacker changing your address first.',
                            'Q: I want “Remember this device” off. A: In profile security settings, clear saved devices, or use private browsing on untrusted hardware. We recommend two-factor sign-in and never saving passwords in simple notes files.',
                        ],
                    ],
                    [
                        'heading' => 'Payments and limits',
                        'paragraphs' => [
                            'Q: Why is my transfer “pending”? A: We may be running extra checks for high-value or new payee payments, or a payment may be queued for a cut-off. Some payments that look unusual are held briefly for a fraud call-back — in those cases, speed up by answering your registered number.',
                            'Q: Can I raise my daily limit? A: In many cases, yes, after we have re-validated you or, for business, a second approver, depending on the mandate you have set. Limits exist to cap loss if a credential is compromised.',
                        ],
                    ],
                    [
                        'heading' => 'Accounts, rates, and fees',
                        'paragraphs' => [
                            'Q: When is interest paid on my savings? A: See the product’s key features document — for many instant-access products it is on the same calendar day each year or on the last day of each month. For some CDs, it is on maturity, or you may be able to elect a periodic pay-away.',
                            'Q: I see a small fee I do not understand. A: The tariff and your statement description line each map to a fee name. If the description does not help, one message in secure online banking with a screenshot and date is often enough to explain or reverse, if we are at fault.',
                        ],
                    ],
                ],
            ],

            'products/cards' => [
                'title' => 'Debit, credit & commercial cards',
                'headline' => 'From tap-and-go in the high street to reconciliation for a fleet',
                'intro' => 'Cards are a payment rail and a control surface: limits, real-time block, and category rules for businesses. The sections below set out the consumer, premium, and business propositions in headline form; your eligibility and the exact product we can offer you depend on a successful application, our card issuer agreement, and your territory.',
                'sections' => [
                    [
                        'heading' => 'Debit on your current account',
                        'paragraphs' => [
                            'A Poise debit card draws in real time, or within the small negative buffer of an agreed overdraft, on your current account. For retail clients you will have contactless, chip-and-PIN, and online use with 3D Secure. You can see authorisations as “pending” until the merchant finalises, which can be a day or more for some hotels and car hire, even after you check out, until they release a hold that was larger than the final amount.',
                        ],
                    ],
                    [
                        'heading' => 'Consumer credit and rewards',
                        'paragraphs' => [
                            'Our credit products may offer a grace period on new purchases, rewards on eligible spend, and, on some tiers, travel insurance, lounge access, or other partner benefits, each governed by a separate pack of terms. The Representative APR, minimum repayment rules, and late-payment charges are in the pre-contract and credit agreement; pay in full to avoid interest on new purchases, where that applies under your product.',
                        ],
                        'list' => [
                            'Introductory and balance transfer offers where we run a campaign, always with end dates and a fee where stated',
                            'Instalment plans and split-ticket post-purchase, where the merchant and network support them',
                        ],
                    ],
                    [
                        'heading' => 'Virtual cards in online banking',
                        'paragraphs' => [
                            'On suitable accounts you may be issued a virtual view of a card, including a masked number for display in-app and controls to freeze. Not every virtual token can be used at every in-person terminal; the physical plastic remains the default for shop floors where a profile includes one.',
                        ],
                    ],
                ],
                'disclaimer' => 'The Poise group may issue cards through a licensed partner or a group entity, subject to the operator’s and card scheme’s rules. Credit is subject to status, age, and our lending criteria. Fees, interest, and non-repayment consequences are as set out in the agreement. Always read the full terms for each product you take.',
            ],
        ];
    }

    public function show(string $key): View
    {
        $pages = self::content();
        abort_unless(isset($pages[$key]), 404);
        $data = $pages[$key];
        $current = str_replace(['/', '-'], ['_', '_'], $key);

        $accents = config('poise_media.section_accents', []);
        $hero = config('poise_media.heroes')[$key] ?? config('poise_media.default_hero');
        $n = max(1, count($accents));
        $sectionImages = collect($data['sections'])->keys()->map(fn (int $i) => $accents[$i % $n])->values()->all();

        $view = match ($key) {
            'personal' => 'pages.personal',
            'loans' => 'pages.loans',
            'wealth' => 'pages.wealth',
            'fdr' => 'pages.fdr',
            'products/cards' => 'pages.cards',
            default => 'pages.content',
        };

        return view($view, array_merge($data, [
            'current' => $current,
            'heroImage' => $hero,
            'sectionImages' => $sectionImages,
        ]));
    }

    public static function routeNameForKey(string $key): string
    {
        return 'public.' . str_replace(['/', '-'], ['_', '_'], $key);
    }
}
