<?php
namespace App;

use Orchid\Screen\Actions\Link;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Layout;
use App\Orchid\Layouts\Subscription\SubscriptionFiltersLayoutEmail;
use App\Orchid\Layouts\Subscription\SubscriptionFiltersLayoutFullname;
use App\Orchid\Layouts\Subscription\SubscriptionFiltersLayoutServiceName;
use App\Orchid\Layouts\Subscription\SubscriptionFiltersLayout;
use App\Orchid\Layouts\Subscription\SubscriptionEditLayout;
use App\Orchid\Layouts\Subscription\SubscriptionListLayout;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\EmailLog;
use Orchid\Support\Facades\Toast;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubscriptionStatusNotification;
use App\Mail\ExpirationReminder30Days;
use App\Mail\ExpirationReminder5Days;
use App\Mail\ExpirationReminder0Days;
use Illuminate\Mail\Mailable;

class FunctionHelper {
    public function getEmailSubject($emailView)
    {
        switch ($emailView) {
            case 'email.expiration_reminder0days':
                return 'Important: Expired';
            case 'email.expiration_reminder5days':
                return 'Important: Expire Soon';
            case 'email.expiration_reminder30days':
                return 'Important: Expire Soon';
            case 'email.subscription_statusnotification':
                return 'Update';
        }
    }
}

