<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApprovalLog;
use App\Models\MobileUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MobileUserApprovalController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'pending');

        $users = MobileUser::query()
            ->where('status', $status)
            ->latest()
            ->paginate(15);

        return view('admin.approvals.index', [
            'status' => $status,
            'users' => $users,
        ]);
    }

    public function show(MobileUser $mobileUser): View
    {
        return view('admin.approvals.show', [
            'mobileUser' => $mobileUser,
        ]);
    }

    public function approve(MobileUser $mobileUser, Request $request): RedirectResponse
    {
        if ($mobileUser->status === 'approved') {
            return back()->with('status', 'Akun sudah disetujui sebelumnya.');
        }

        $adminId = Auth::guard('admin')->id();

        $mobileUser->forceFill([
            'status' => 'approved',
            'approved_at' => Carbon::now(),
            'approved_by_admin_id' => $adminId,
            'rejected_at' => null,
            'rejected_by_admin_id' => null,
            'rejection_reason' => null,
        ])->save();

        ApprovalLog::query()->create([
            'mobile_user_id' => $mobileUser->id,
            'admin_id' => $adminId,
            'decision' => 'approved',
            'notes' => $request->input('notes'),
            'decision_at' => Carbon::now(),
        ]);

        return redirect()
            ->route('admin.approvals.index')
            ->with('status', 'Akun berhasil disetujui.');
    }

    public function reject(MobileUser $mobileUser, Request $request): RedirectResponse
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $adminId = Auth::guard('admin')->id();

        $mobileUser->forceFill([
            'status' => 'rejected',
            'rejected_at' => Carbon::now(),
            'rejected_by_admin_id' => $adminId,
            'rejection_reason' => $data['reason'],
        ])->save();

        ApprovalLog::query()->create([
            'mobile_user_id' => $mobileUser->id,
            'admin_id' => $adminId,
            'decision' => 'rejected',
            'notes' => $data['reason'],
            'decision_at' => Carbon::now(),
        ]);

        return redirect()
            ->route('admin.approvals.index', ['status' => 'pending'])
            ->with('status', 'Akun ditolak.');
    }
}
