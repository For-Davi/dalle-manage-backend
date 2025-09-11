<?php

namespace App\Http\Controllers;

use App\Http\Requests\Notification\DeleteNotificationRequest;
use App\Http\Requests\Notification\UpdateReadNotificationRequest;
use App\Repositories\NotificationRepository;
use App\Utils\ErrorLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController
{
    public function __construct(
        private NotificationRepository $repository
    ) {}

    public function index(Request $request)
    {
        try {
            $notifications = $this->repository->getAllByUserId($request->user()->id);

            return response()->json(['notifications' => $notifications], 200);
        } catch (\Exception $e) {
            ErrorLogger::log('Erro ao buscar notificações:', $e, $request);

            return response()->json(['message' => 'Erro ao buscar notificações'], 500);
        }
    }

    public function updateRead(UpdateReadNotificationRequest $request)
    {
        try {
            DB::beginTransaction();

            $notification = $this->repository->markAsRead($request->notificationID);

            DB::commit();

            return response()->json(['notification' => $notification], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao atualizar notificação:', $e, $request);

            return response()->json(['message' => 'Erro ao atualizar notificação'], 500);
        }
    }

    public function destroy(DeleteNotificationRequest $request)
    {
        try {
            DB::beginTransaction();

            $notification = $this->repository->delete($request->notificationID);

            if ($notification) {
                DB::commit();
                $notifications = $this->repository->getAllByUserId($request->user()->id);

                return response()->json(['notifications' => $notifications, 'message' => 'Notificação excluída'], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            ErrorLogger::log('Erro ao excluir notificação:', $e, $request);

            return response()->json(['message' => 'Erro ao excluir notificação'], 500);
        }
    }
}
