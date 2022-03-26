<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use Grav\Common\Page\Page;
use Grav\Common\Page\Pages;
use Grav\Common\User\User;
use RocketTheme\Toolbox\Event\Event;

class MaintenancePlugin extends Plugin
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0]
        ];
    }

    /**
     * Initialize configuration
     */
    public function onPluginsInitialized()
    {
        if ($this->isAdmin()) {
            $this->active = false;
            return;
        }

        $this->enable([
            'onPagesInitialized' => ['onPagesInitialized', 1000000],
            'onTwigTemplatePaths' => ['onTwigTemplatePaths', 0],
        ]);
    }

    /**
     * Initialize a maintenance page
     *
     * @param Event $event
     */
    public function onPagesInitialized(Event $event)
    {
        $config = $this->config();

        if (!$config['active']) {
            return;
        }

        // Additional user authenticated check, if login plugin is enabled
        if ($this->config->get('plugins.login.enabled', false)) {
            /** @var User $user */
            $user = $this->grav['user'];
            if ($config['allow_login'] && $user->authenticated && $user->authorize($config['login_access'] ?: 'site.login')) {
                // User has been logged in and has permission to access the site when it is in maintenance mode.
                return;
            }
        }

        $pageEvent = new Event();
        $pageEvent->config = $config;
        $pageEvent->page = null;

        // First attempt to get maintenance page by firing getMaintenancePage event.
        $this->grav->fireEvent('getMaintenancePage', $pageEvent);

        /** @var Page $page */
        $page = isset($pageEvent->page) ? $pageEvent->page : null;

        if (!$page) {
            // Get the custom page route if specified
            $custom_page_route = $this->config->get('plugins.maintenance.maintenance_page_route');

            if ($custom_page_route) {
                /** @var Pages $pages */
                $pages = $this->grav['pages'];

                // Try to load user error page.
                $page = $pages->dispatch($custom_page_route, true);
            }
        }

        // If no page found yet, use the built-in one...
        if (!$page) {
            $page = new Page;
            $page->init(new \SplFileInfo(__DIR__ . "/pages/maintenance.md"));
        }

        // Set default expires for maintenance page.
        $header = $page->header();
        if (!isset($header->expires)) {
            $page->expires(0);
        }

        // unset the old page, and use the new one
        unset($this->grav['page']);

        // Set good header
        $page->modifyHeader('http_response_code', $this->config->get('plugins.maintenance.maintenance_status_code', 503));

        $this->grav['page'] = $page;

        $this->enable([
            'onPageInitialized' => ['onPageInitialized', 1000000],
            'onTwigSiteVariables' => ['onTwigSiteVariables', 0]
        ]);

        // Site is on maintenance, prevent other plugins from running.
        $event->stopPropagation();
    }

    /**
     * @param Event $event
     */
    public function onPageInitialized(Event $event)
    {
        // NOTE: We must add the form here and not in the onFormPageHeaderProcessed event.
        // The mentioned event will run before onPagesInitialized, thatswhy the form will be
        // added at this later stage.
        $this->grav['page']->addForms([$this->grav['config']->get('plugins.maintenance.form')]);

        $this->grav->fireEvent('onMaintenancePage', $event);

        // Site is on maintenance, prevent other plugins from running.
        $event->stopPropagation();
    }

    /**
     * Add current directory to twig lookup paths.
     */
    public function onTwigTemplatePaths()
    {
        $this->grav['twig']->twig_paths[] = __DIR__ . '/templates';
    }

    /**
     * Set needed variables to display the maintenance page.
     */
    public function onTwigSiteVariables()
    {
        // Make sure the plugin is active
        $config = $this->config();
        if (!$config['active']) {
            return;
        }

        // Additional user authenticated check, if login plugin is enabled
        if ($this->config->get('plugins.login.enabled', false)) {
            /** @var User $user */
            $user = $this->grav['user'];
            if ($user->authenticated) {
                return;
            }
        }

        $this->grav['twig']->twig_vars['maintenance'] = $config;
    }
}
