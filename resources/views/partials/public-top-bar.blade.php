<div
    class="w-full text-xs border-b border-slate-200/80 fixed top-0 left-0 right-0 z-50 theme-pc-topbar"
    style="background: #f0f2f4;"
>
    <div class="max-w-7xl mx-auto px-6 h-9 flex items-center justify-between text-slate-600">
        <div class="space-x-4">
            <a
                href="{{ route('public.personal') }}"
                class="nav-link-animated text-slate-600 hover:text-[#003b70] {{ ($current ?? '') === 'personal' ? 'is-active nav-pc-active' : '' }}"
            >Personal</a>
            <a
                href="{{ route('public.business') }}"
                class="nav-link-animated text-slate-600 hover:text-[#003b70] {{ ($current ?? '') === 'business' ? 'is-active nav-pc-active' : '' }}"
            >Business</a>
            <a
                href="{{ route('public.commercial') }}"
                class="nav-link-animated text-slate-600 hover:text-[#003b70] {{ ($current ?? '') === 'commercial' ? 'is-active nav-pc-active' : '' }}"
            >Commercial</a>
        </div>
        <div class="space-x-4 hidden sm:flex sm:items-center">
            <a href="{{ route('public.customer_service') }}" class="nav-link-animated text-slate-600 hover:text-[#003b70]">Customer service</a>
            <a href="{{ route('public.atms_and_branches') }}" class="nav-link-animated text-slate-600 hover:text-[#003b70]">Find ATM or branch</a>
            <a href="{{ route('public.security_center') }}" class="nav-link-animated text-slate-600 hover:text-[#003b70]">Security Center</a>
        </div>
    </div>
</div>
