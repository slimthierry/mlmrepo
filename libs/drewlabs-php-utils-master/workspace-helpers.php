<?php

/**
 * Checks if the current connected user has acces to admin ressources
 *
 * @return boolean
 */
function drewlabs_has_admin_access()
{
    return app(\Illuminate\Contracts\Auth\Access\Gate::class)->allows('is-admin');
}

/**
 * Gate policy checking if a workspace is a pulic workspace. Returns false if not
 *
 * @param int|string|\Drewlabs\Contracts\Data\IModelable
 * @return boolean
 */
function drewlabs_is_public_workspace($workspace)
{
    return app(\Illuminate\Contracts\Auth\Access\Gate::class)->allows('is-public-workspace', array($workspace));
}

/**
 * Checks if the current request user is a workspace administrator. Workspace administrators can performs actions
 * like adding new users to the workspace, deleting users from the workspace, and performing other actions that can be performed
 * by the moderator and other users
 *
 * @param int|string|\Drewlabs\Contracts\Data\IModelable
 * @return boolean
 */
function drewlabs_is_workspace_administrator($workspace)
{
    return \drewlabs_has_admin_access() || app(\Illuminate\Contracts\Auth\Access\Gate::class)->allows('is-workspace-administrator', array($workspace));
}

/**
 * Checks if the current request user is a workspace moderator. As it can performs actions like
 * editing and deleting other users posts
 *
 * @param int|string|\Drewlabs\Contracts\Data\IModelable
 * @return boolean
 */
function drewlabs_is_workspace_moderator($workspace)
{
    return drewlabs_has_admin_access() || \drewlabs_is_workspace_administrator($workspace) || app(\Illuminate\Contracts\Auth\Access\Gate::class)->allows('is-workspace-moderator', array($workspace));
}

/**
 * Access gate restricting user to only edit item of a workspace if they have the edit/update authorization on that workspace
 *
 * @param int|string|\Drewlabs\Contracts\Data\IModelable
 * @return boolean
 */
function drewlabs_has_edit_workspace_item_access($workspace)
{
    return drewlabs_is_workspace_moderator($workspace) || app(\Illuminate\Contracts\Auth\Access\Gate::class)->allows('can-edit-workspace-item', array($workspace));
}

/**
 * Access gate restricting user to only delete/remove/hide item of a workspace if they have delete authorization on that workspace
 *
 * @param int|string|\Drewlabs\Contracts\Data\IModelable
 * @return boolean
 */
function drewlabs_has_delete_workspace_item_access($workspace)
{
    return drewlabs_is_workspace_moderator($workspace) || app(\Illuminate\Contracts\Auth\Access\Gate::class)->allows('can-delete-workspace-item', array($workspace));
}

//
/**
 * Checks if a request user belongs to a given workspace and has authorizations on that worspace
 *
 * @param int|string|\Drewlabs\Contracts\Data\IModelable
 * @return boolean
 */
function drewlabs_belongs_to_workspace($workspace)
{
    return app(\Illuminate\Contracts\Auth\Access\Gate::class)->allows('belongs-to-workspace', array($workspace));
}

/**
 * Checks if a request has a given ability on a model attached to a workspace
 *
 * @param Illuminate\Http\Request $request
 * @param \Drewlabs\Contracts\Data\IModelable $forum
 * @param int|string|null $workspace
 * @return void
 */
function request_user_has_abilty_on_workspace(Illuminate\Http\Request $request, \Drewlabs\Contracts\Data\IModelable $model, $ability, $workspace = null)
{
    if (isset($workspace)) {
        // If the user is a workspace administrator, it/he can update the workspace forum details
        if (\drewlabs_is_workspace_administrator($workspace)) {
            return true;
        }
        // If the request user is a worksapce moderator, he can edit the forum if the forum was created by
        // him, therefore we apply the forum model policy to the request
        if (\drewlabs_is_workspace_moderator($workspace) && $request->user()->can($ability, $model)) {
            return true;
        }
    } else {
        // Apply the forum model policy to the the request action if there is no workspace attached to the forum
        if ($request->user()->can($ability, $model)) {
            return true;
        }
    }
    return false;
}

/**
 * Checks if request user can pulish an item to a forum if provided
 *
 * @param \Drewlabs\Contracts\Data\IModelable $forum
 * @return bool
 */
function drewlabs_user_can_publish_to_forum(\Drewlabs\Contracts\Data\IModelable $forum)
{
    if (!is_null($forum) && !is_null($forum->workspace)) {
        $workspace = $forum->workspace->getKey();
        // The request user must be at a workspace moderator in order to create a forum
        return \drewlabs_belongs_to_workspace($workspace);
    }
    return true;
}

/**
 * Checks if a user can edit or delete an item/pulication from a forum
 *
 * @param \Illuminate\Http\Request $request
 * @param \Drewlabs\Contracts\Data\IModelable $post
 * @param string $ability
 * @param int|string|null $forum
 * @return boolean
 */
function can_user_alter_forum_publication(\Illuminate\Http\Request $request, \Drewlabs\Contracts\Data\IModelable $post, $ability, $forum = null)
{
    if (\drewlabs_has_admin_access()) {
        return true;
    }
    $forum = isset($forum) ? $forum : $post->forum_id;
    if (isset($forum)) {
        $result = app(\Drewlabs\Packages\Forums\Forum\Models\Forum::class)->find($forum);
        if (!is_null($result) && !is_null($result->workspace)) {
            $workspace = $result->workspace->getKey();
            // Post can be updated if user is the workspace moderator
            if (\drewlabs_is_workspace_moderator($workspace)) {
                return true;
            }
            // Post can be updated if request user belongs to the workspace and the post was created by him
            return \drewlabs_belongs_to_workspace($workspace) && $request->user()->can($ability, $post);
        }
    }
    return true;
}

/**
 * Checks if user can delete a given workspace type
 *
 * @param \Drewlabs\Contracts\Data\IModelable $model
 * @return boolean
 */
function drewlabs_can_delete_workspace_type(\Drewlabs\Contracts\Data\IModelable $model)
{
    if (in_array($model->getKey(), \drewlabs_workspace_configs('default_workspace_type_ids'))) {
        return false;
    }
    if (in_array(strtolower($model->label), \drewlabs_workspace_configs('default_workspace_type_label'))) {
        return false;
    }
    return true;
}

/**
 * Checks if user can delete a given workspace authorization
 *
 * @param \Drewlabs\Contracts\Data\IModelable $model
 * @return boolean
 */
function drewlabs_can_delete_workspace_authorization(\Drewlabs\Contracts\Data\IModelable $model)
{
    if (in_array(strtolower($model->label), \drewlabs_workspace_configs('workspace_authorizations'))) {
        return false;
    }
    return true;
}
