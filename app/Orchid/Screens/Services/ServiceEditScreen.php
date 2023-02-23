<?php

namespace App\Orchid\Screens\Services;

use Orchid\Screen\Screen;
use Orchid\Screen\Actions\Button;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use App\Models\Service;
use App\Orchid\Layouts\Service\ServiceEditLayout;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ServiceEditScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public $service;
    public function query(Service $service): iterable
    {
        return [
            'service'       => $service
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return $this->service->exists ? 'Edit Service' : 'Create Service';
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
            Button::make(__('Create service'))
                ->icon('list')
                ->method('createOrUpdate')
                ->canSee(!$this->service->exists),

            Button::make(__('Remove'))
                ->icon('trash')
                ->confirm(__('Once the service is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.'))
                ->method('remove')
                ->canSee($this->service->exists),

            Button::make(__('Update'))
                ->icon('check')
                ->method('createOrUpdate')
                ->canSee($this->service->exists),
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
            Layout::block(ServiceEditLayout::class)
                ->title(__('Service Information'))
                ->description(__('Update your account\'s profile information and email address.'))
                ->commands(
                    Button::make(__('Update'))
                        ->type(Color::DEFAULT())
                        ->icon('check')
                        ->canSee($this->service->exists)
                        ->method('createOrUpdate')
                ),
        ];
    }

    public function createOrUpdate(Service $service, Request $request)
    {
        $service->fill($request->get('service'))->save();

        Toast::info(__('Service was created.'));

        return redirect()->route('platform.systems.services');
    }
}
