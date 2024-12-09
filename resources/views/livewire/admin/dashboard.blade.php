<div>
    <x-card title="Admin Dashboard" subtitle="Overview of admin activities" shadow separator>
        {{-- Top Metrics --}}
        <div class="flex flex-wrap justify-between gap-4 mb-6">
            <x-stat title="Gross Revenue" value="€{{ number_format($totalRevenue ?? 0, 2) }}" icon="s-currency-euro" class="flex-1 min-w-[200px] drop-shadow-lg" />
            <x-stat title="Orders" value="{{ $totalOrders ?? 0 }}" icon="s-gift" class="flex-1 min-w-[200px] drop-shadow-lg" />
            <x-stat title="New Customers" value="{{ $newCustomers ?? 0 }}" icon="o-user-plus" class="flex-1 min-w-[200px] drop-shadow-lg" />
            <x-stat title="Active Products" value="{{ $activeProducts ?? 0 }}" icon="m-archive-box-arrow-down" class="flex-1 min-w-[200px] drop-shadow-lg" />
        </div>

        {{-- Charts --}}
        <div class="flex flex-wrap justify-between gap-4 mb-6">
            <div class="flex-1 min-w-[300px] shadow-lg p-4 ">
                <h3 class="text-lg font-semibold mb-2">Gross Revenue</h3>
                <x-chart wire:model="grossRevenueChart" class="h-64" />
            </div>
            <div class="flex-1 min-w-[300px] shadow-lg p-4 ">
                <h3 class="text-lg font-semibold mb-2">Product Categories</h3>
                <x-chart wire:model="productCategoryChart" class="h-64" />
            </div>
        </div>

        {{-- Tables --}}
        <div class="flex flex-wrap justify-between gap-4">
            {{-- Top Customers Table --}}
            <div class="flex-1 min-w-[300px] shadow-lg p-4 ">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Top Customers</h3>
                    <x-button icon="o-arrow-long-right" link="/admin/customers" label="View All" class="btn-ghost btn-sm" />
                </div>
                <x-table :headers="$topCustomersHeaders" :rows="$topCustomersRows">
                    @scope('cell_name', $row)
                        {{ $row['name'] ?? 'N/A' }}
                    @endscope

                    @scope('cell_email', $row)
                        {{ $row['email'] ?? 'N/A' }}
                    @endscope

                    @scope('cell_revenue', $row)
                        €{{ number_format($row['revenue'] ?? 0, 2) }}
                    @endscope
                </x-table>
            </div>

            {{-- Best-Selling Products Table --}}
            <div class="flex-1 min-w-[300px] shadow-lg p-4 ">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Best-Selling Products</h3>
                    <x-button icon="o-arrow-long-right" link="/admin/products" label="View All" class="btn-ghost btn-sm" />
                </div>
                <x-table :headers="$bestSellingProductsHeaders" :rows="$bestSellingProductsRows">
                    @scope('cell_name', $row)
                        {{ $row['name'] ?? 'N/A' }}
                    @endscope

                    @scope('cell_price', $row)
                        €{{ number_format($row['price'] ?? 0, 2) }}
                    @endscope

                    @scope('cell_sales', $row)
                        {{ $row['sales'] ?? 0 }}
                    @endscope
                </x-table>
            </div>
        </div>
    </x-card>
</div>
