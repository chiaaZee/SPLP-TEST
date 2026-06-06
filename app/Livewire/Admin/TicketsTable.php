<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SupportTicket;
use Illuminate\Support\Facades\Auth;

class TicketsTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $statusFilter = 'open'; // Default to open for Task Dashboard
    public $perPage = 10;

    public $showBanner = true;

    // Reply Logic
    public $replyingTicket = null;
    public $replyMessage = '';
    public $replyStatus = 'resolved';

    // Listeners
    protected $listeners = ['refreshTable' => '$refresh'];

    public function render()
    {
        $tickets = SupportTicket::query()
            ->with(['user'])
            ->when($this->statusFilter, function($q) {
                $q->where('status', $this->statusFilter);
            })
            ->where(function($q) {
                $q->where('subject', 'like', '%'.$this->search.'%')
                  ->orWhere('message', 'like', '%'.$this->search.'%')
                  ->orWhereHas('user', function($qu) {
                      $qu->where('name', 'like', '%'.$this->search.'%');
                  });
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.admin.tickets-table', [
            'tickets' => $tickets
        ]);
    }

    public function openReplyModal($id)
    {
        $ticket = SupportTicket::find($id);
        if($ticket) {
            $this->replyingTicket = $ticket;
            $this->replyMessage = $ticket->admin_reply ?? '';
            $this->replyStatus = $ticket->status;
            $this->dispatch('open-reply-modal');
        }
    }

    public function submitReply()
    {
        $this->validate([
            'replyMessage' => 'required|string',
            'replyStatus' => 'required|in:open,in_progress,resolved,closed'
        ]);

        if ($this->replyingTicket) {
            $this->replyingTicket->update([
                'admin_reply' => $this->replyMessage,
                'status' => $this->replyStatus,
                'replied_at' => now()
            ]);

            $this->dispatch('close-reply-modal');
            $this->dispatch('swal:toast', type: 'success', message: 'Balasan tiket berhasil dikirim!');
            $this->dispatch('update-notifications');
            $this->dispatch('refreshTable');

            // Reset
            $this->replyingTicket = null;
            $this->replyMessage = '';
        }
    }
}
