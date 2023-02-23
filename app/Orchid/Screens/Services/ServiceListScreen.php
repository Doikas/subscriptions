<?php

namespace App\Orchid\Screens\Services;

use Orchid\Screen\Screen;
use App\Orchid\Layouts\Service\ServiceFiltersLayout;
use App\Orchid\Layouts\Service\ServiceEditLayout;
use App\Orchid\Layouts\Service\ServiceListLayout;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Orchid\Screen\Actions\Link;
use Orchid\Support\Facades\Layout;
use Orchid\Support\Facades\Toast;
use App\Models\Service;

class ServiceListScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'services' => Service::filters(ServiceFiltersLayout::class)->defaultSort('id')->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Services';
    }

    public function description(): ?string
    {
        return 'All registered services';
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
                ->route('platform.systems.services.create'),
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
            ServiceFiltersLayout::class,
            ServiceListLayout::class,

            Layout::modal('asyncEditServiceModal', ServiceEditLayout::class)
                ->async('asyncGetService'),
        ];
    }

    public function asyncGetService(Service $service): iterable
    {
        return [
            'service' => $service,
        ];
    }

    public function saveService(Request $request, Service $service): void
    {

        $service->fill($request->input('service'))->save();

        Toast::info(__('Service was saved.'));
    }

    public function remove(Request $request): void
    {
        Service::findOrFail($request->get('id'))->delete();

        Toast::info(__('Service was removed'));
    }
}
