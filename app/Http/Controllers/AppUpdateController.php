<?php

namespace App\Http\Controllers;

use Native\Desktop\Facades\AutoUpdater;
use Throwable;

class AppUpdateController extends Controller
{
    public function quitAndInstall()
    {
        try {
            AutoUpdater::quitAndInstall();
        } catch (Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Atualização automática disponível apenas no aplicativo desktop.',
            ], 422);
        }

        return response()->json(['ok' => true]);
    }

    public function check()
    {
        try {
            AutoUpdater::checkForUpdates();
        } catch (Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Atualização automática disponível apenas no aplicativo desktop.',
            ], 422);
        }

        return response()->json(['ok' => true]);
    }
}
