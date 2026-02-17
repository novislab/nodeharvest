<div class="space-y-6">
    <x-dashboard.page-header :breadcrumbs="[['href' => route('dashboard'), 'label' => 'Dashboard']]" />

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-dashboard.metric-card label="Unique Visitors" value="24.7K" change="+20%" change-type="positive" />
        <x-dashboard.metric-card label="Total Pageviews" value="55.9K" change="+4%" change-type="positive" />
        <x-dashboard.metric-card label="Bounce Rate" value="54%" change="-1.59%" change-type="negative" />
        <x-dashboard.metric-card label="Visit Duration" value="2m 56s" change="+7%" change-type="positive" />
    </div>

    <livewire:dashboard.revenue-chart />
</div>
