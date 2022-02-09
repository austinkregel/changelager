<?php

namespace App\Http\Controllers;

use App\Models\Repository;
use Illuminate\Http\Request;

class RepositoryController extends Controller
{
    public function index()
    {
        return Repository::query()
            ->with('team')
            ->where('team_id', auth()->user()->current_team_id)
            ->get();
    }

    public function store(Request $request)
    { 
        abort_unless($request->user()->hasTeamPermission($request->user()->currentTeam, 'create'), 403);
     
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
            'url' => 'required|string|max:2048',
            'is_public' => 'required|boolean',
            'use_v_in_version' => 'required|boolean',
        ]);

        $resource = auth()->user()->currentTeam->repositories()->create($data);
        return $resource->refresh();
    }

    public function show(Request $request, Repository $repository)
    {
        abort_unless($repository->team_id === auth()->user()->current_team_id, 404);
        
        abort_unless($request->user()->hasTeamPermission($repository->team, 'read'), 404);

        return $repository;
    }

    public function update(Request $request, Repository $repository)
    {
        abort_unless($repository->team_id === $request->user()->current_team_id, 404);
 
        abort_unless($request->user()->hasTeamPermission($repository->team, 'create'), 403);
 
        $repository->update($request->all());

        return $repository->refresh();
    }

    public function destroy(Request $request, Repository $repository)
    {
        abort_unless($repository->team_id === $request->user()->current_team_id, 404);
 
        abort_unless($request->user()->hasTeamPermission($repository->team, 'create'), 403);

        $repository->delete();
        return response([], 204);
    }
}
