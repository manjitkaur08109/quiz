<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
public function index(Request $request)
{    $notification = auth()->user()->notifications()->latest();
    
    $notificatuion = $notification->paginate($request->per_page ?? 5);
    return $this->actionSuccess('Notifications successfully fetched',
    $notificatuion
    );
}

public function show($id)
{
    $notification = auth()->user()
        ->notifications()
        ->where('id', $id)
        ->firstOrFail();

    return $this->actionSuccess('Notification show',$notification);
}

public function markAsRead($id)
{
    $notification = auth()->user()
        ->unreadNotifications()
        ->where('id', $id)
        ->first();

    if ($notification) {
        $notification->markAsRead();
    }

    return $this->actionSuccess('Notification marked as read');
}

public function destroy($id)
{
    auth()->user()
        ->notifications()
        ->where('id', $id)
        ->delete();

    return $this->actionSuccess( 'Notification deleted');
}

public function destroyAll()
{
    auth()->user()->notifications()->delete();

    return $this->actionSuccess('All notifications deleted');
}

}
