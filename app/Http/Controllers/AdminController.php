<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $ticketsQuery = Ticket::with('parkingLot')->orderBy('created_at', 'desc');
        $searchTicket = $request->query('search_ticket');
        if ($searchTicket) {
            $ticketsQuery->where(function ($q) use ($searchTicket) {
                $q->where('ticket_code', 'like', '%' . $searchTicket . '%')
                  ->orWhere('plate_number', 'like', '%' . $searchTicket . '%');
            });
        }
        $tickets = $ticketsQuery->paginate(10);

        $usersQuery = User::orderBy('created_at', 'desc');
        $searchUser = $request->query('search_user');
        if ($searchUser) {
            $usersQuery->where(function ($q) use ($searchUser) {
                $q->where('name', 'like', '%' . $searchUser . '%')
                  ->orWhere('email', 'like', '%' . $searchUser . '%');
            });
        }
        $users = $usersQuery->paginate(10);

        return view('admin.dashboard', compact('tickets', 'users'));
    }

    public function ticketsEdit(Ticket $ticket)
    {
        return response()->json([
            'id' => $ticket->id,
            'ticket_code' => $ticket->ticket_code,
            'plate_number' => $ticket->plate_number,
            'vehicle_type' => $ticket->vehicle_type,
            'vehicle_model' => $ticket->vehicle_model,
            'entry_time' => $ticket->entry_time ? $ticket->entry_time->format('Y-m-d\TH:i') : '',
            'exit_time' => $ticket->exit_time ? $ticket->exit_time->format('Y-m-d\TH:i') : '',
            'status' => $ticket->status,
            'fee' => $ticket->fee,
            'lot_number' => $ticket->parkingLot->lot_number ?? '',
        ]);
    }

    public function ticketsUpdate(Request $request, Ticket $ticket)
    {
        $request->validate([
            'plate_number' => 'required|string|max:20|regex:/^[A-Z]{1,2}\\s?\\d{1,4}\\s?[A-Z]{1,3}$/',
            'vehicle_type' => 'required|in:mobil,motor,truk',
            'vehicle_model' => 'nullable|string|max:50',
            'entry_time' => 'required|date',
            'exit_time' => 'nullable|date|after:entry_time',
            'status' => 'required|in:entry,paid',
            'fee' => 'nullable|numeric|min:0',
        ]);

        $ticket->update([
            'plate_number' => strtoupper($request->plate_number),
            'vehicle_type' => $request->vehicle_type,
            'vehicle_model' => $request->vehicle_model ?? '',
            'entry_time' => $request->entry_time,
            'exit_time' => $request->exit_time,
            'status' => $request->status,
            'fee' => $request->fee ?? $ticket->calculateFee(),
        ]);

        return response()->json(['success' => true, 'message' => 'Ticket updated successfully!']);
    }

    public function ticketsDestroy(Ticket $ticket)
    {
        $ticket->delete();

        return response()->json(['success' => true, 'message' => 'Ticket deleted successfully!']);
    }

    public function usersIndex()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);

        return response()->json($users);
    }

    public function usersEdit(User $user)
    {
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'is_admin' => $user->is_admin,
        ]);
    }

    public function usersUpdate(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:8|confirmed',
            'is_admin' => 'boolean',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'is_admin' => $request->is_admin ?? false,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return response()->json(['success' => true, 'message' => 'User updated successfully!']);
    }

    public function usersDestroy(User $user)
    {
        $user->delete();

        return response()->json(['success' => true, 'message' => 'User deleted successfully!']);
    }
}

