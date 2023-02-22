<?php
declare(strict_types=1);
namespace App\Orchid\Screens\Customer;

use Orchid\Screen\Screen;
use App\Orchid\Layouts\Customer\CustomerFiltersLayout;
use App\Orchid\Layouts\Customer\CustomerEditLayout;
use App\Orchid\Layouts\Customer\CustomerListLayout;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use App\Models\Customer;

class CustomerListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'customers' => Customer::filters()->defaultSort('id')->paginate(),
                
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Customers';
    }

    public function description(): ?string
    {
        return 'All registered customers';
    }
    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Link::make(__('Add'))
                ->icon('plus')
                ->route('platform.systems.customers.create'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            CustomerFiltersLayout::class,
            CustomerListLayout::class,

            Layout::modal('asyncEditCustomerModal', CustomerEditLayout::class)
                ->async('asyncGetCustomer'),
        ];
    }

    public function asyncGetCustomer(Customer $customer): iterable
    {
        return [
            'customer' => $customer,
        ];
    }

    public function saveCustomer(Request $request, Customer $customer): void
    {
        $request->validate([
            'customer.email' => [
                'required',
                Rule::unique(Customer::class, 'email')->ignore($customer),
            ],
        ]);

        $customer->fill($request->input('customer'))->save();

        Toast::info(__('Customer was saved.'));
    }

    public function remove(Request $request): void
    {
        Customer::findOrFail($request->get('id'))->delete();

        Toast::info(__('Customer was removed'));
    }
}
