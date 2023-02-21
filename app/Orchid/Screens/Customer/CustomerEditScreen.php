<?php

namespace App\Orchid\Screens\Customer;

use App\Orchid\Layouts\Role\RolePermissionLayout;
use App\Orchid\Layouts\Customer\CustomerEditLayout;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Orchid\Access\UserSwitch;
use Orchid\Platform\Models\User;
use Orchid\Screen\Action;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use App\Models\Customer;

class CustomerEditScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public $customer;
    public function query(Customer $customer): iterable
    {
        return [
            'customer'       => $customer
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->customer->exists ? 'Edit User' : 'Create User';
    }

    public function description(): ?string
    {
        return 'Details';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            Button::make(__('Create customer'))
                ->icon('user')
                ->method('createOrUpdate')
                ->canSee(!$this->customer->exists),

            Button::make(__('Remove'))
                ->icon('trash')
                ->confirm(__('Once the customer is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.'))
                ->method('remove')
                ->canSee($this->customer->exists),

            Button::make(__('Update'))
                ->icon('check')
                ->method('createOrUpdate')
                ->canSee($this->customer->exists),
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
            Layout::block(CustomerEditLayout::class)
                ->title(__('Profile Information'))
                ->description(__('Update your account\'s profile information and email address.'))
                ->commands(
                    Button::make(__('Update'))
                        ->type(Color::DEFAULT())
                        ->icon('check')
                        ->canSee($this->customer->exists)
                        ->method('createOrUpdate')
                ),
        ];
    }

    public function createOrUpdate(Customer $customer, Request $request)
    {
        $customer->fill($request->get('customer'))->save();

        Toast::info(__('Customer was created.'));

        return redirect()->route('platform.systems.customers');
    }
}
