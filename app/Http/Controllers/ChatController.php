<?php

namespace App\Http\Controllers;

use App\Events\MessageEvent;
use App\Models\Message;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ChatController extends Controller
{
    public function index(Request $request): Response
    {
        $users = User::where('id', '!=', $request->user()->id)->get();
        return Inertia::render('Chat', compact('users'));
    }

    public function show(Request $request, $id): JsonResponse
    {
        $messages = Message::query()
            ->where('sender', $request->user()->id)
            ->where('receiver', $id)
            ->orWhere('receiver', $request->user()->id)
            ->where('sender', $id)
            ->get();
        return response()->json([
            'status' => ResponseAlias::HTTP_OK,
            'data' => $messages
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'receiver' => 'required|integer',
            'message' => 'required'
        ]);

        if ($validator->fails()){
            return response()->json([
                'status' => ResponseAlias::HTTP_NOT_ACCEPTABLE,
                'error' => $validator->getMessageBag()->getMessages()
            ]);
        }

        try {
            $message = $request->user()->messages()->create([
                'receiver' => $request->get('receiver'),
                'message' => $request->get('message')
            ]);

            if (empty($message)){
                throw new Exception('Could not create message');
            }
            MessageEvent::dispatch($message);
            return response()->json([
                'status' => ResponseAlias::HTTP_OK,
                'data' => $message,
                'message' => 'Message send successfully'
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => ResponseAlias::HTTP_BAD_REQUEST,
                'error' => $exception->getMessage()
            ]);
        }
    }
}
