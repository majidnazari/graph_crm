<?php

namespace App\Providers;

use App\Events\ChangeAllStudentCallsEvent;
use App\Events\ChangeAllStudentPurchasesEvent;
use App\Events\RemoveAllStudentFromClassRoomEvent;
use App\Events\ChangeAllStudentSanadsEvent;
use App\Events\RemoveAllStudentTagsEvent;
use App\Events\RemoveAllStudentTempreturesEvent;
use App\Events\RemoveAllStudentCollectionsEvent;
use App\Events\RemoveAllSupporterHistoriesEvent;
use App\Events\CreateLogMergedStudentEvent;

use App\Listeners\ChangeCallsListener;
use App\Listeners\ChangeSanadListener;
use App\Listeners\ChangePurchasesListener;
use App\Listeners\RemoveStudentCollectionsListener;
use App\Listeners\RemoveStudentTagsListener;
use App\Listeners\RemoveStudentTempreturesListener;
use App\Listeners\RemoveClassRoomsListener;
use App\Listeners\RemoveSupporterHistoriesListener;
use App\Listeners\CreateLogMergedStudentListener;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Student;
use App\Observers\StudentObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
            ChangeSanadListener::class,
            ChangeCallsListener::class,
            ChangePurchasesListener::class,

        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        Student::observe(StudentObserver::class);

        Event::listen(
            ChangeAllStudentCallsEvent::class,
            [ChangeCallsListener::class, 'handle']
        );

        Event::listen(
            ChangeAllStudentSanadsEvent::class,
            [ChangeSanadListener::class, 'handle']

        );

        Event::listen(
            ChangeAllStudentPurchasesEvent::class,
            [ChangePurchasesListener::class, 'handle']
        );

        Event::listen(
            RemoveAllStudentTagsEvent::class,
            [RemoveStudentTagsListener::class, 'handle']
        );

        Event::listen(
            RemoveAllStudentFromClassRoomEvent::class,
            [RemoveClassRoomsListener::class, 'handle']

        );

        Event::listen(
            RemoveAllStudentCollectionsEvent::class,
            [RemoveStudentCollectionsListener::class, 'handle']
        );
        Event::listen(
            RemoveAllStudentTempreturesEvent::class,
            [RemoveStudentTempreturesListener::class, 'handle']
        );

        Event::listen(
            RemoveAllSupporterHistoriesEvent::class,
            [RemoveSupporterHistoriesListener::class, 'handle']
        );

        Event::listen(
            CreateLogMergedStudentEvent::class,
            [ CreateLogMergedStudentListener::class, 'handle']
        );
        

        // Event::listen(
        //     ChangeAllStudentSanadEvent::class,
        //     [ChangeSanadListener::class, 'handle']
        // );

        // Event::listen(function (ChangeAllStudentSanadsEvent $event) {
        //     //
        // });
        // Event::listen(function (ChangeAllStudentCallsEvent $event) {
        //     //
        // });
        // Event::listen(function (ChangeAllStudentPurchasesEvent $event) {
        //     //
        // });
    }
}
