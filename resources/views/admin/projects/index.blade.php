@extends('admin.layouts.app')

@section('title', 'Projekte')
@section('heading', 'Projekte')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <p class="text-gray-600">{{ $projects->count() }} Projekte</p>
    </div>
    @can('create', App\Models\Project::class)
    <a href="{{ route('admin.projects.create') }}" 
       class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
        + Neues Projekt
    </a>
    @endcan
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subdomain</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">App-Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Begriffe</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guides</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tutorials</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Users</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aktionen</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($projects as $project)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ $project->name }}</div>
                    <div class="text-sm text-gray-500">{{ $project->club_name }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $project->slug }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    @if($project->subdomain)
                        <code class="bg-gray-100 px-2 py-1 rounded text-xs">{{ $project->subdomain }}.{{ config('app.domain', 'localhost') }}</code>
                    @else
                        <span class="text-gray-400">—</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $project->app_name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $project->terms_count }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $project->guides_count }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $project->tutorials_count }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $project->users_count }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($project->is_active)
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktiv</span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Inaktiv</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    @can('view', $project)
                    <a href="{{ route('admin.projects.edit', $project) }}" 
                       class="text-blue-600 hover:text-blue-900 mr-3">Bearbeiten</a>
                    @endcan
                    @can('delete', $project)
                    <form action="{{ route('admin.projects.destroy', $project) }}" 
                          method="POST" 
                          class="inline"
                          onsubmit="return confirm('Sind Sie sicher, dass Sie dieses Projekt löschen möchten? Alle zugehörigen Daten werden gelöscht.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">Löschen</button>
                    </form>
                    @endcan
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="px-6 py-4 text-center text-gray-500">
                    Keine Projekte gefunden. Erstellen Sie Ihr erstes Projekt!
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
