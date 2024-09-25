<?php
declare(strict_types=1);
namespace App\Orchid\Screens\EmailLog;

use Orchid\Screen\Screen;
use Illuminate\Http\Request;
use App\Models\EmailLog;
use App\Orchid\Layouts\EmailLog\EmailLogListLayout;
use Orchid\Support\Facades\Layout;
use App\Orchid\Layouts\EmailLog\EmailLogFiltersLayout;
use App\Orchid\Layouts\EmailLog\EmailLogFiltersLayoutTable;

class EmailLogListScreen extends Screen
{
    
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {

        return [
            'subscription_email_logs' => EmailLog::with('subscription', 'subscription.service', 'subscription.customer')
                ->filters(EmailLogFiltersLayout::class)
                ->filters(EmailLogFiltersLayoutTable::class)
                ->defaultSort('sent_at', 'desc')
                ->paginate(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Email Logs';
    }
    public function description(): ?string
    {
        return 'List of email logs';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            EmailLogFiltersLayout::class,
            EmailLogListLayout::class,
        ];
    }
}
