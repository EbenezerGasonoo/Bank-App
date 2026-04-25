<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WireCodeRequest;
use App\Notifications\WireCodesIssuedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminWireCodeRequestController extends Controller
{
    public function index()
    {
        $requests = WireCodeRequest::with(['user', 'resolver'])
            ->latest('requested_at')
            ->paginate(25);

        return view('admin.wire-requests.index', compact('requests'));
    }

    public function issue(Request $request, WireCodeRequest $wireRequest)
    {
        $request->validate([
            'pin' => ['required', 'string', 'min:4', 'max:12'],
            'tax_code' => ['required', 'string', 'min:3', 'max:32'],
            'imf_code' => ['required', 'string', 'min:3', 'max:32'],
            'cot_code' => ['required', 'string', 'min:3', 'max:32'],
            'admin_note' => ['nullable', 'string', 'max:500'],
        ]);

        if ($wireRequest->status !== 'pending') {
            return back()->withErrors(['request' => 'This request has already been processed.']);
        }

        $user = $wireRequest->user;
        $user->update([
            'wire_pin_hash' => Hash::make($request->pin),
            'tax_code_hash' => Hash::make($request->tax_code),
            'imf_code_hash' => Hash::make($request->imf_code),
            'cot_code_hash' => Hash::make($request->cot_code),
        ]);

        $wireRequest->update([
            'status' => 'issued',
            'resolved_at' => now(),
            'resolved_by' => $request->user()->id,
            'admin_note' => $request->admin_note,
        ]);

        $user->notify(new WireCodesIssuedNotification(
            (string) $request->pin,
            (string) $request->tax_code,
            (string) $request->imf_code,
            (string) $request->cot_code
        ));

        return back()->with('success', 'Wire transfer codes issued to customer by email.');
    }
}

