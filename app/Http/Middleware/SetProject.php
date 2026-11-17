<?php

namespace App\Http\Middleware;

use App\Services\ProjectResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetProject
{
    /**
     * ProjectResolver Instanz
     */
    protected ProjectResolver $resolver;

    /**
     * Konstruktor
     */
    public function __construct(ProjectResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * Handle incoming request.
     *
     * Erkennt das aktuelle Projekt und speichert es in der Request.
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $project = $this->resolver->resolveFromRequest($request);

        // Projekt in der Request speichern
        $request->attributes->set('project', $project);

        return $next($request);
    }
}
