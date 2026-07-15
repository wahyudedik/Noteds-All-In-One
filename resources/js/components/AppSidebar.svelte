<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import BookOpen from 'lucide-svelte/icons/book-open';
    import FolderGit2 from 'lucide-svelte/icons/folder-git-2';
    import LayoutGrid from 'lucide-svelte/icons/layout-grid';
    import FileText from 'lucide-svelte/icons/file-text';
    import Search from 'lucide-svelte/icons/search';
    import Tag from 'lucide-svelte/icons/tag';
    import type { Snippet } from 'svelte';
    import AppLogo from '@/components/AppLogo.svelte';
    import NavFooter from '@/components/NavFooter.svelte';
    import NavMain from '@/components/NavMain.svelte';
    import NavUser from '@/components/NavUser.svelte';
    import TeamSwitcher from '@/components/TeamSwitcher.svelte';
    import {
        Sidebar,
        SidebarContent,
        SidebarFooter,
        SidebarHeader,
        SidebarMenu,
        SidebarMenuButton,
        SidebarMenuItem,
    } from '@/components/ui/sidebar';
    import { dashboard } from '@/routes';
    import type { NavItem, Team } from '@/types';

    let {
        children,
    }: {
        children?: Snippet;
    } = $props();

    const currentTeam = $derived(page.props.currentTeam as Team | null);
    const teamSlug = $derived(currentTeam?.slug || '');
    const dashboardUrl = $derived(
        currentTeam ? dashboard(currentTeam.slug) : '/',
    );

    const mainNavItems = $derived<NavItem[]>([
        {
            title: 'Dashboard',
            href: dashboardUrl,
            icon: LayoutGrid,
        },
        {
            title: 'Catatan',
            href: teamSlug ? `/${teamSlug}/notes` : '/',
            icon: FileText,
        },
        {
            title: 'Pencarian',
            href: teamSlug ? `/${teamSlug}/search` : '/',
            icon: Search,
        },
    ]);

    const footerNavItems: NavItem[] = [
        {
            title: 'Repository',
            href: 'https://github.com/your-username/noteds-all-in-one',
            icon: FolderGit2,
        },
        {
            title: 'Documentation',
            href: 'https://laravel.com/docs',
            icon: BookOpen,
        },
    ];
</script>

<Sidebar collapsible="icon" variant="inset">
    <SidebarHeader>
        <SidebarMenu>
            <SidebarMenuItem>
                <SidebarMenuButton size="lg" asChild>
                    {#snippet children(props)}
                        <Link
                            {...props}
                            href={dashboardUrl}
                            class={props.class}
                        >
                            <AppLogo />
                        </Link>
                    {/snippet}
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
        <SidebarMenu>
            <SidebarMenuItem>
                <TeamSwitcher />
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarHeader>

    <SidebarContent>
        <NavMain items={mainNavItems} />
    </SidebarContent>

    <SidebarFooter>
        <NavFooter items={footerNavItems} />
        <NavUser />
    </SidebarFooter>
</Sidebar>
{@render children?.()}
