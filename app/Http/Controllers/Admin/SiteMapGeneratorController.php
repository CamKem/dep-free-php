<?php

namespace App\Http\Controllers\Admin;

use App\Core\FileSystem\Storage;
use App\Core\Routing\Route;
use App\Core\Routing\Router;

class SiteMapGeneratorController
{
    public function __invoke()
    {
        $routes = app()->resolve(Router::class)->getRoutes();
        $sitemap = $this->generateSiteMap($routes);
        $updated = $this->updateSiteMapFile($sitemap);
        if ($updated !== false) {
            return redirect(route('admin.index'))->with('flash-message', 'Site map generated successfully.');
        }
    }

    private function generateSiteMap($routes): string
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8" ?>' . PHP_EOL;
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        /** @var Route $route */
        foreach ($routes->getRoutes() as $name => $route) {
            if (strtolower($route->getMethod()) === 'get'
                && empty(array_intersect(['admin', 'auth'], $route->getMiddleware()))
                && !preg_match('/\{.*?}/', $route->uri())
            ) {
                $sitemap .= '    <url>' . PHP_EOL;
                $sitemap .= '        <loc>' . url($route->uri()) . '</loc>' . PHP_EOL;
                $sitemap .= '        <lastmod>' . date('Y-m-d') . '</lastmod>' . PHP_EOL;
                $sitemap .= '        <changefreq>daily</changefreq>' . PHP_EOL;
                $sitemap .= '        <priority>0.8</priority>' . PHP_EOL;
                $sitemap .= '    </url>' . PHP_EOL;
            }
        }

        $sitemap .= '</urlset>' . PHP_EOL;

        return $sitemap;
    }

    private function updateSiteMapFile($sitemap): string|false
    {
        return storage()->put('sitemap.xml', $sitemap);
    }
}