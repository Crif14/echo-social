<?php

namespace App\Http\Controllers;

use App\Models\DailyTopic;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DailyTopicController extends Controller
{
    public function index()
    {
        $topics = DailyTopic::with('user')
            ->orderBy('topicDate', 'desc')
            ->get();

        return view('topics.index', compact('topics'));
    }

    public function create()
    {
        return view('topics.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|min:3|max:100',
            'description' => 'nullable|max:500',
        ]);

        DailyTopic::create([
            'userId' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'topicDate' => Carbon::today(),
        ]);

        return redirect()->route('posts.index');
    }
}