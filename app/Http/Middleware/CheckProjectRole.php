<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class CheckProjectRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // احصل على المشروع من الطلب (يمكنك تخصيص ذلك حسب حاجتك)
        $project = Project::find($request->route('project'));

        if (!$project) {
            return response()->json(['error' => 'Project not found'], 404);
        }

        // تحقق مما إذا كان المستخدم جزءًا من المشروع وله الدور المطلوب
        if (!Auth::user()->projects()->where('project_id', $project->id)->wherePivot('role', $role)->exists()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return $next($request);
    }
}
