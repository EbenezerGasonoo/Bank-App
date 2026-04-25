<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CardController extends Controller
{
    public function index()
    {
        $accounts = Auth::user()->accounts()->with('cards')->get();
        return view('cards.index', compact('accounts'));
    }

    public function toggleFreeze(Card $card)
    {
        // Ensure user owns this card
        $accountIds = Auth::user()->accounts->pluck('id');
        abort_unless($accountIds->contains($card->account_id), 403);

        $card->update(['is_frozen' => !$card->is_frozen]);
        $status = $card->is_frozen ? 'frozen' : 'unfrozen';
        return back()->with('success', "Card has been {$status}.");
    }
}
