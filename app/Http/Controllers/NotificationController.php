<?php

namespace App\Http\Controllers;

use App\Http\Requests\Notification\DeleteNotificationRequest;
use App\Http\Requests\Notification\UpdateReadNotificationRequest;
use App\Repositories\NotificationRepository;
use Illuminate\Http\Request;

class NotificationController extends BaseController
{
    public function __construct(
        private NotificationRepository $repository
    ) {}

    public function index(Request $request)
    {
        return $this->safeExecute(function () {
            $notifications = $this->repository->getAllByUser();

            return response()->json(['notifications' => $notifications], 200);
        }, 'Erro ao buscar notificações', $request);
    }

    public function updateRead(UpdateReadNotificationRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $notification = $this->repository->markAsRead($request->notificationID);

            return response()->json(['notification' => $notification], 200);
        }, 'Erro ao atualizar notificação', $request);
    }

    public function destroy(DeleteNotificationRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->repository->delete($request->notificationID);
            $notifications = $this->repository->getAllByUser();

            return response()->json(['notifications' => $notifications, 'message' => 'Notificação excluída'], 200);
        }, 'Erro ao excluir notificação', $request);
    }
}
