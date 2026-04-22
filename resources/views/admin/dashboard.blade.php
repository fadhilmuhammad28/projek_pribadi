@extends('layouts.app')
@include('layouts.navigation')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight flex items-center">
        <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
        </svg>
        Administrator Panel
    </h2>
@endsection

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">

                {{-- Tabs --}}
                <div class="flex border-b border-gray-200 dark:border-gray-700 mb-8">
                    <button onclick="switchTab('tickets')" class="py-4 px-6 font-semibold border-b-2 border-indigo-500 text-indigo-600 dark:text-indigo-400" id="tickets-tab">🅿️ Data Parkir ({{ $tickets->total() }})</button>
                    <button onclick="switchTab('users')" class="py-4 px-6 font-semibold text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 ml-1" id="users-tab">👥 Pengguna ({{ $users->total() }})</button>
                </div>

                {{-- Tickets Tab --}}
                <div id="tickets-tab-content" class="tab-content">
                    {{-- Search --}}
                    <form method="GET" action="/admin/dashboard" class="mb-8 p-6 bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-xl">
                        <div class="flex flex-col md:flex-row gap-4">
                            <input type="text" name="search_ticket" value="{{ request('search_ticket') }}" placeholder="Cari ticket code atau plat nomor..." class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                            <div class="flex gap-2">
                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-lg">🔍 Filter</button>
                                <a href="{{ route('admin.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-lg">↻ Reset</a>
                            </div>
                        </div>
                    </form>

                    {{-- Tickets Table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto bg-white dark:bg-gray-800 rounded-xl shadow-lg">
                            <thead class="bg-gradient-to-r from-purple-500 to-indigo-600 text-white">
                                <tr>
                                    <th class="px-6 py-4 text-left rounded-tl-xl font-bold">Ticket</th>
                                    <th class="px-4 py-4 text-left font-bold">Plat</th>
                                    <th class="px-4 py-4 text-left font-bold">Jenis</th>
                                    <th class="px-4 py-4 text-left font-bold">Lot</th>
                                    <th class="px-4 py-4 text-left font-bold">Status</th>
                                    <th class="px-4 py-4 text-left font-bold">Biaya</th>
                                    <th class="px-6 py-4 text-left rounded-tr-xl font-bold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($tickets as $ticket)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-6 py-4 font-mono font-bold text-indigo-600">{{ $ticket->ticket_code }}</td>
                                    <td class="px-4 py-4 font-medium">{{ $ticket->plate_number }}</td>
                                    <td class="px-4 py-4">
                                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ ucfirst($ticket->vehicle_type) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 font-bold text-lg text-gray-900 dark:text-gray-100">{{ $ticket->parkingLot->lot_number ?? 'N/A' }}</td>
                                    <td class="px-4 py-4">
                                        @if($ticket->status === 'entry')
                                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">Entry</span>
                                        @else
                                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">Paid</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 font-bold text-green-600">Rp {{ number_format($ticket->fee ?? 0) }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex gap-2">
                                            <button onclick="editTicket({{ $ticket->id }})" class="text-blue-600 hover:text-blue-900 font-medium text-sm p-2 hover:bg-blue-50 rounded-lg transition">✏️ Edit</button>
                                            <button onclick="deleteTicket({{ $ticket->id }}, '{{ $ticket->ticket_code }}')" class="text-red-600 hover:text-red-900 font-medium text-sm p-2 hover:bg-red-50 rounded-lg transition">🗑️ Hapus</button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        <div class="text-6xl mb-4">📋</div>
                                        <p>Belum ada data parkir</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-8">
                        {{ $tickets->appends(request()->query())->links() }}
                    </div>
                </div>

                {{-- Users Tab --}}
                <div id="users-tab-content" class="tab-content hidden">
                    {{-- Search --}}
                    <form method="GET" action="{{ route('admin.dashboard') }}" class="mb-8 p-6 bg-gradient-to-r from-pink-50 to-rose-50 dark:from-pink-900/20 dark:to-rose-900/20 rounded-xl">
                        <div class="flex flex-col md:flex-row gap-4">
                            <input type="text" name="search_user" value="{{ request('search_user') }}" placeholder="Cari nama atau email..." class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-pink-500">
                            <div class="flex gap-2">
                                <button type="submit" class="bg-pink-600 hover:bg-pink-700 text-white px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-lg">🔍 Filter</button>
                                <a href="{{ route('admin.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-xl font-semibold shadow-md hover:shadow-lg">↻ Reset</a>
                            </div>
                        </div>
                    </form>

                    {{-- Users Table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto bg-white dark:bg-gray-800 rounded-xl shadow-lg">
                            <thead class="bg-gradient-to-r from-pink-500 to-rose-600 text-white">
                                <tr>
                                    <th class="px-6 py-4 text-left rounded-tl-xl font-bold">Nama</th>
                                    <th class="px-6 py-4 text-left font-bold">Email</th>
                                    <th class="px-4 py-4 text-left font-bold">Admin</th>
                                    <th class="px-6 py-4 text-left rounded-tr-xl font-bold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($users as $user)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-6 py-4 font-semibold text-gray-900 dark:text-gray-100">{{ $user->name }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $user->email }}</td>
                                    <td class="px-4 py-4">
                                        @if($user->is_admin)
                                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">Admin</span>
                                        @else
                                            <span class="px-3 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">User</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex gap-2">
                                            <button onclick="editUser({{ $user->id }})" class="text-blue-600 hover:text-blue-900 font-medium text-sm p-2 hover:bg-blue-50 rounded-lg transition">✏️ Edit</button>
                                            @if(!$user->is_admin)
                                            <button onclick="deleteUser({{ $user->id }}, '{{ addslashes($user->name) }}')" class="text-red-600 hover:text-red-900 font-medium text-sm p-2 hover:bg-red-50 rounded-lg transition">🗑️ Hapus</button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        <div class="text-6xl mb-4">👥</div>
                                        <p>Belum ada pengguna</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-8">
                        {{ $users->appends(request()->query())->links() }}
                    </div>
                </div>

                {{-- Edit Ticket Modal --}}
                <div id="edit-ticket-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full" role="document">
                            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left flex-1">
                                        <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white mb-6" id="modal-title">
                                            ✏️ Edit Data Parkir
                                        </h3>
                                        <form id="edit-ticket-form">
                                            @csrf
                                            <input type="hidden" id="ticket-id" name="id">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Ticket Code</label>
                                                    <input type="text" id="ticket-code" disabled class="w-full px-4 py-3 border border-gray-300 rounded-xl bg-gray-100">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Plat Nomor *</label>
                                                    <input type="text" id="ticket-plate" name="plate_number" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Jenis Kendaraan *</label>
                                                    <select id="ticket-type" name="vehicle_type" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                                                        <option value="mobil">Mobil</option>
                                                        <option value="motor">Motor</option>
                                                        <option value="truk">Truk</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Model Kendaraan</label>
                                                    <input type="text" id="ticket-model" name="vehicle_model" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Waktu Masuk *</label>
                                                    <input type="datetime-local" id="ticket-entry" name="entry_time" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Waktu Keluar</label>
                                                    <input type="datetime-local" id="ticket-exit" name="exit_time" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status *</label>
                                                    <select id="ticket-status" name="status" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                                                        <option value="entry">Entry</option>
                                                        <option value="paid">Paid</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Biaya (Rp)</label>
                                                    <input type="number" id="ticket-fee" name="fee" step="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="button" onclick="updateTicket()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-6 py-3 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm transition">
                                    💾 Update
                                </button>
                                <button type="button" onclick="closeModal('edit-ticket-modal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-6 py-3 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-600 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-500 transition">❌ Batal</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Edit User Modal --}}
                <div id="edit-user-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="document">
                            <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                <div class="sm:flex sm:items-start">
                                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left flex-1">
                                        <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white mb-6" id="modal-title">
                                            ✏️ Edit Pengguna
                                        </h3>
                                        <form id="edit-user-form">
                                            @csrf
                                            <input type="hidden" id="user-id" name="id">
                                            <div class="mb-6">
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama *</label>
                                                <input type="text" id="user-name" name="name" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-pink-500">
                                            </div>
                                            <div class="mb-6">
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email *</label>
                                                <input type="email" id="user-email" name="email" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-pink-500">
                                            </div>
                                            <div class="mb-6">
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password Baru (kosongkan jika tidak diubah)</label>
                                                <input type="password" id="user-password" name="password" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-pink-500">
                                                <input type="password" id="user-password-confirm" name="password_confirmation" class="mt-2 w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-pink-500">
                                            </div>
                                            <div class="mb-6">
                                                <label class="flex items-center">
                                                    <input type="checkbox" id="user-is-admin" name="is_admin" class="rounded border-gray-300 text-pink-600 shadow-sm focus:ring-pink-500">
                                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300 font-medium">Administrator</span>
                                                </label>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button type="button" onclick="updateUser()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-6 py-3 bg-pink-600 text-base font-medium text-white hover:bg-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 sm:ml-3 sm:w-auto sm:text-sm transition">
                                    💾 Update
                                </button>
                                <button type="button" onclick="closeModal('edit-user-modal')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-6 py-3 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-pink-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm dark:bg-gray-600 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-500 transition">❌ Batal</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
function switchTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active from all tabs
    document.querySelectorAll('[id$="-tab"]').forEach(tab => {
        tab.classList.remove('border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400');
        tab.classList.add('text-gray-500', 'hover:text-gray-700', 'dark:text-gray-400', 'dark:hover:text-gray-200');
    });
    
    // Show selected tab
    document.getElementById(tabName + '-tab-content').classList.remove('hidden');
    document.getElementById(tabName + '-tab').classList.add('border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400');
    document.getElementById(tabName + '-tab').classList.remove('text-gray-500', 'hover:text-gray-700', 'dark:text-gray-400', 'dark:hover:text-gray-200');
}

function editTicket(id) {
    fetch(`/admin/tickets/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('ticket-id').value = data.id;
            document.getElementById('ticket-code').value = data.ticket_code;
            document.getElementById('ticket-plate').value = data.plate_number;
            document.getElementById('ticket-type').value = data.vehicle_type;
            document.getElementById('ticket-model').value = data.vehicle_model;
            document.getElementById('ticket-entry').value = data.entry_time;
            document.getElementById('ticket-exit').value = data.exit_time || '';
            document.getElementById('ticket-status').value = data.status;
            document.getElementById('ticket-fee').value = data.fee;
            document.getElementById('edit-ticket-modal').classList.remove('hidden');
        });
}

function editUser(id) {
    fetch(`/admin/users/${id}/edit`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('user-id').value = data.id;
            document.getElementById('user-name').value = data.name;
            document.getElementById('user-email').value = data.email;
            document.getElementById('user-is-admin').checked = data.is_admin;
            document.getElementById('edit-user-modal').classList.remove('hidden');
        });
}

function updateTicket() {
    const id = document.getElementById('ticket-id').value;
    const form = document.getElementById('edit-ticket-form');
    const formData = new FormData(form);
    
    fetch(`/admin/tickets/${id}`, {
        method: 'PUT',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Update failed'));
        }
    });
}

function updateUser() {
    const id = document.getElementById('user-id').value;
    const form = document.getElementById('edit-user-form');
    const formData = new FormData(form);
    
    fetch(`/admin/users/${id}`, {
        method: 'PUT',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Update failed'));
        }
    });
}

function deleteTicket(id, code) {
    if (confirm(`Hapus ticket ${code}?`)) {
        fetch(`/admin/tickets/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function deleteUser(id, name) {
    if (confirm(`Hapus pengguna ${name}?`)) {
        fetch(`/admin/users/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

// Load tickets tab by default
switchTab('tickets');
</script>

@endsection