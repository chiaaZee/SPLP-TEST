<?php

namespace App\Http\Controllers;

use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AdminTicketController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $tickets = SupportTicket::with('user')->latest();
            return DataTables::of($tickets)
                ->addIndexColumn()
                ->editColumn('created_at', fn($row) => $row->created_at->format('d M Y H:i'))
                ->addColumn('user_name', fn($row) => $row->user->name ?? '-')
                ->addColumn('priority_badge', fn($row) => $row->priority_badge)
                ->addColumn('status_badge', fn($row) => $row->status_badge)
                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-sm btn-primary view-ticket" data-id="' . $row->id . '"><i class="ti ti-eye me-1"></i>Lihat</button>';
                })
                ->rawColumns(['priority_badge', 'status_badge', 'action'])
                ->make(true);
        }
        return view('content.admin.tickets.index');
    }

    public function show($id)
    {
        $ticket = SupportTicket::with('user')->findOrFail($id);
        return response()->json($ticket);
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'admin_reply' => 'required|string',
            'status' => 'required|in:open,in_progress,resolved,closed'
        ]);

        $ticket = SupportTicket::findOrFail($id);
        $ticket->update([
            'admin_reply' => $request->admin_reply,
            'status' => $request->status,
            'replied_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Balasan berhasil dikirim!']);
    }
}
