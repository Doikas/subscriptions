<?php

namespace App\Orchid\Screens\Subscriptions;

use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use Illuminate\Validation\Rule;
use App\Models\Subscription;
use App\Models\Service;
use App\Models\Customer;
use App\Orchid\Layouts\Subscription\SubscriptionEditLayout;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\DB;

class SubscriptionEditScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public $subscription;
    public function query(Subscription $subscription): iterable
    {
        
        return [
            
            'subscription'        => $subscription,
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->subscription->exists ? 'Edit Subscription' : 'Create Subscription';
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
            Button::make(__('Create subscription'))
                ->icon('list')
                ->method('createOrUpdate')
                ->canSee(!$this->subscription->exists),

            Button::make(__('Remove'))
                ->icon('trash')
                ->confirm(__('Once the subscription is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.'))
                ->method('remove', ['subscription' => $this->subscription])
                ->canSee($this->subscription->exists),

            Button::make(__('Update'))
                ->icon('check')
                ->method('createOrUpdate')
                ->canSee($this->subscription->exists),
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
            Layout::block(SubscriptionEditLayout::class)
                ->title(__('Subscription Information'))
                ->description(__('Please fill in the required information to create or update a new subscription.'))
                ->commands(
                    Button::make(__('Update'))
                        ->type(Color::DEFAULT())
                        ->icon('check')
                        ->canSee($this->subscription->exists)
                        ->method('createOrUpdate')
                ),
        ];
    }

    public function createOrUpdate(Subscription $subscription, Request $request)
    {


        $subscription->fill($request->get('subscription'))->save();

        Toast::info(__('Subscription was created.'));
        $previousUrl = $request->input('previous_url');
        
        return redirect($previousUrl);
    }

    public function remove(Subscription $subscription)
    {
        $subscription->delete();

        Toast::info(__('Subscription was removed'));

        return redirect()->back();
    }
}
