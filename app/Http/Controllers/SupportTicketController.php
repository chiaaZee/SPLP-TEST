<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SupportTicketController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $tickets = SupportTicket::where('user_id', auth()->id())->latest();
            return DataTables::of($tickets)
                ->addIndexColumn()
                ->editColumn('created_at', fn($row) => $row->created_at->format('d M Y H:i'))
                ->addColumn('priority_badge', fn($row) => $row->priority_badge)
                ->addColumn('status_badge', fn($row) => $row->status_badge)
                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-sm btn-primary view-ticket" data-id="' . $row->id . '"><i class="ti ti-eye"></i></button>';
                })
                ->rawColumns(['priority_badge', 'status_badge', 'action'])
                ->make(true);
        }
        return view('content.user.tickets.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'priority' => 'required|in:low,medium,high'
        ]);

        SupportTicket::create([
            'user_id' => auth()->id(),
            'subject' => $request->subject,
            'message' => $request->message,
            'priority' => $request->priority,
            'status' => 'open'
        ]);

        return response()->json(['success' => true, 'message' => 'Tiket berhasil dikirim!']);
    }

    public function show($id)
    {
        $ticket = SupportTicket::where('user_id', auth()->id())->findOrFail($id);
        return response()->json($ticket);
    }
}
