<?php


if (!function_exists('file_inputs_to_attributes')) {

    /**
     * Transformation helper converting request file input into an associative array
     * with a format known by the handler service
     *
     * @param \Illuminate\Http\Request|array $inputs
     * @param int $connectedUserId
     * @param int|string $connectedUserFolderBasename
     * @param boolean $isBase64
     * @param boolean $isFileList
     * @return array
     */
    function file_inputs_to_attributes($inputs, $connectedUserId = null, $connectedUserFolderBasename = null, $isBase64 = true, $isFileList = false)
    {
        $files = ($inputs instanceof \Illuminate\Http\Request) ? \file_from_illuminate_request($inputs, $isFileList) : collect($isFileList ? $inputs : [$inputs]);
        // Get folders from files input
        $folders = $files->map(function ($i) {
            return isset($i['folder_id']) ? $i['folder_id'] : null;
        })->filter(function ($item) {
            return !is_null($item);
        });
        $result = \get_folders_by_ids($folders->toArray());
        // Set the full path for each file
        $files = $files->map(function ($input) use ($result, $connectedUserFolderBasename, $isBase64, $connectedUserId) {
            $filename = \generate_filename($input['extension']);
            $folderPath = isset($input['folder_id']) ? $result->get($input['folder_id'])->first()->fullpath : null;
            return array_merge(
                $input,
                array(
                    'name' => $filename,
                    'user_id' => $connectedUserId,
                    'storage_path' => \join_paths(
                        array_merge(isset($connectedUserFolderBasename) ? [$connectedUserFolderBasename] : [], isset($folderPath) ? [$folderPath] : [], [$filename])
                    ),
                    'is_based64_encoded' => $isBase64
                )
            );
        })->toArray();
        return $files;
    }
}

if (!function_exists('file_from_illuminate_request')) {

    /**
     * Returns file(s) entry from the request body
     *
     * @param Request $request
     * @param boolean $isFileList
     * @return \Illuminate\Support\Collection
     */
    function file_from_illuminate_request(\Illuminate\Http\Request $request, $isFileList = false)
    {
        $key = (new \Drewlabs\Packages\UploadedFile\Models\UploadedFile())->setMultiple($isFileList)->getRequestInput();
        return collect($isFileList ?
            (isset($key) ? $request->get($key) : $request->all()) :
            [$request->all()]);
    }
}

if (!function_exists('get_folders_by_ids')) {

    /**
     * Returns the path to the folder in which the file will be saved
     *
     * @param string[]|int[] $ids
     * @return \Illuminate\Support\Collection
     */
    function get_folders_by_ids($ids)
    {
        // Get folders matching the provided entries
        $result = app(\Drewlabs\Packages\Database\Extensions\BaseIlluminateModelRepository::class)
            ->setModel(\Drewlabs\Packages\UploadedFile\Models\Folder::class)
            ->pushFilter(
                app(\Drewlabs\Contracts\Data\IModelFilter::class)->setQueryFilters(
                    array(
                        'whereIn' => array('id', $ids)
                    )
                )
            )->find(array(), array('id', 'fullpath'));
        return $result->groupBy('id');
    }
}

if (!function_exists('generate_filename')) {

    /**
     * Generate a new filename using the uuid algorithm
     *
     * @param string $extension
     * @return string
     */
    function generate_filename($extension)
    {
        return \strtolower(\Drewlabs\Utils\Rand::guid()) . '.' . $extension;
    }
}


if (!function_exists('join_paths')) {
    /**
     * Combine the folder path with the file name returning the file full path
     *
     * @param string[] $paths
     * @return string
     */
    function join_paths(array $paths)
    {
        $paths = array_map(function ($item) {
            return \Drewlabs\Utils\Str::rtrim($item, DIRECTORY_SEPARATOR);
        }, $paths);
        return \Drewlabs\Utils\Str::concat(DIRECTORY_SEPARATOR, ...$paths);
    }
}

if (!function_exists('connected_user_folder')) {
    /**
     * Generate a user folder basename string for the connected user
     *
     * @param \Drewlabs\Contracts\Auth\Authenticatable $user
     */
    function connected_user_folder(\Drewlabs\Contracts\Auth\Authenticatable $user)
    {
        $obj = app(\config('drewlabs_uploaded_files.models.users.class', \Drewlabs\Packages\Identity\UserInfo::class));
        // Get the id related to user to be used as user main folder name and file associated user_id
        $id = $obj->fromAuthenticatable($user)->getKey();
        return (string) $id;
    }
}


if (!function_exists('generate_folder_name')) {
    /**
     * Generate a folder basepath string
     *
     * @param string $basename
     */
    function generate_folder_name($basename)
    {
        return \Illuminate\Support\Str::slug($basename, '-') . '_' . time();
    }
}

if (!function_exists('basename_from_foldername')) {

    function basename_from_foldername($foldername)
    {
        return str_replace('-', ' ', \Drewlabs\Utils\Str::before('_', $foldername));
    }
}

if (!function_exists('drewlabs_sys_path_app_path')) {
    /**
     * Takes in a folder path and return a path matching a given application criteria
     *
     * @param string $path
     * @return string
     */
    function drewlabs_sys_path_to_app_path($path)
    {
        // Split paths with DIRECTORY_SEPARATOR
        $parts = \Drewlabs\Utils\Str::toArray($path, DIRECTORY_SEPARATOR);
        // Map through each entries and returns the base name from the folder name
        $parts = array_map(function ($name) {
            return \basename_from_foldername($name);
        }, $parts);
        // Glue base names together with DIRECTORY_SEPARATOR
        // var_dump(\Drewlabs\Utils\Str::fromArray($parts, DIRECTORY_SEPARATOR));
        return \Drewlabs\Utils\Str::fromArray($parts, DIRECTORY_SEPARATOR);
    }
}

if (!function_exists('drewlabs_file_sys_path_to_app_path')) {
    /**
     * Takes in a file storage path and returns a path matching a given application criteria
     *
     * @param string $path
     * @return string
     */
    function drewlabs_file_sys_path_to_app_path($path)
    {
        $filename = pathinfo($path, PATHINFO_BASENAME);
        $folderPath = rtrim(str_replace($filename, '', $path), DIRECTORY_SEPARATOR);
        $folderPath = \drewlabs_sys_path_to_app_path(substr($folderPath, strpos($folderPath, DIRECTORY_SEPARATOR) + 1));
        return empty(trim($folderPath)) ? $filename : $folderPath . DIRECTORY_SEPARATOR . $filename;
    }
}

if (!function_exists('folders_to_tree')) {

    /**
     * Transform a list of folders into a tree view
     *
     * @param \Illuminate\Support\Collection $list
     * @return array
     */
    function folders_list_to_tree_structure(\Illuminate\Support\Collection $list)
    {
        $list = $list->map(function ($folder) {
            if (is_null($folder->parent)) {
                $folder->parent_id = null;
            }
            return $folder;
        });
        // Build folders tree structure from a list of folders using the BFS algorithm
        // Group the folders by parent id in order to ease the search algorithm
        $groups = $list->groupBy('parent_id');
        // Get the top node of the tree structure
        $topNodeFolders = $list->filter(function ($folder) {
            return is_null($folder->parent);
        });
        $getNodeChildrenFn = function ($index) use ($groups) {
            return $groups->get($index);
        };
        // Get the child nodes for a provided parent node while the parent node
        // still have child node using recursion algorithm
        $mapChildNodes = function ($folder) use (&$mapChildNodes, $getNodeChildrenFn) {
            $childNodes = $getNodeChildrenFn($folder->getKey());
            if (is_null($childNodes)) {
                $folder->setRelation('children', collect([]));
            } else {
                $folder->setRelation('children', $childNodes->map(function ($value) use ($mapChildNodes) {
                    return $mapChildNodes($value);
                }));
            }
            return $folder;
        };
        $values = $topNodeFolders->map(function ($folder) use ($mapChildNodes) {
            return $mapChildNodes($folder);
        });
        return array_values($values->toArray());
    }
}


/**
 * Takes a collection of folders model object and return a list of path data transfert object
 *
 * @param \Illuminate\Support\Collection $folders
 * @return \Drewlabs\Packages\UploadedFile\Models\Dto\PathDataTransfertObject[]
 */
function drewlabs_folders_to_path_dto_structure(\Illuminate\Support\Collection $folders)
{
    if ($folders) {
        return array_values($folders->map(function($folder){
            return \drewlabs_folder_to_path_dto($folder);
        })->all());
    }
    return [];
}

/**
 * Takes a collection of uploaded files and return a list of path data transfert object
 *
 * @param \Illuminate\Support\Collection $files
 * @return \Drewlabs\Packages\UploadedFile\Models\Dto\PathDataTransfertObject[]
 */
function drewlabs_files_to_klm_structure(\Illuminate\Support\Collection $files)
{
    if ($files) {
        return array_values($files->map(function ($file) {
            $isShared = !$file->sharings->isEmpty() || !$file->workspace_file_sharings->isEmpty();
            return \drewlabs_file_to_path_dto($file, null, [
                "sharedBy" =>  $isShared ? $file->by() : null,
                "isShared" => $isShared
            ]);
        })->all());
    }
    return [];
}

/**
 * Takes a collection of shared files and return a list of path data transfert object
 *
 * @param \Illuminate\Support\Collection $shared_files
 * @return \Drewlabs\Packages\UploadedFile\Models\Dto\PathDataTransfertObject[]
 */
function drewlabs_shared_files_to_klm_structure(\Illuminate\Support\Collection $shared_files, $sharedWith = null)
{
    if ($shared_files) {
        return array_values($shared_files->filter(function($shared_file){
            return isset($shared_file->file);
        })->map(function ($shared_file) use ($sharedWith) {
            return \drewlabs_file_to_path_dto($shared_file->file, $shared_file->getAuthorizationsAttribute(), [
                "sharedWith" => is_null($sharedWith) ? $shared_file->shared_with() : $sharedWith,
                "sharedBy" => !is_null($shared_file->file) ? $shared_file->file->by() : null,
                "sharedDate" => $shared_file->created_at,
                "isShared" => true
            ]);
        })->all());
    }
    return [];
}
/**
 * Takes in an instance of the folder model and convert it attributes into an array of path dto object
 *
 * @param \Illuminate\Support\Collection $folder
 * @return \Drewlabs\Packages\UploadedFile\Models\Dto\PathDataTransfertObject[]
 */
function drewlabs_folder_with_files_to_path_dto($folder)
{
    if ($folder) {
        $files = $folder->files;
        $paths = [];
        if ($files && !$files->isEmpty()) {
            $paths = $files->map(function ($file) {
                $isShared = !$file->sharings->isEmpty() || !$file->workspace_file_sharings->isEmpty();
                return \drewlabs_file_to_path_dto($file, null, [
                    "sharedBy" =>  $isShared ? $file->by() : null,
                    "isShared" => $isShared
                ]);
            })->all();
        }
        $paths[] = \drewlabs_folder_to_path_dto($folder);
        return $paths;
    }
    return [];
}


function drewlabs_file_with_folder_to_path_dto($file, $authorizations = null, $sharingDetails = [])
{
    if ($file && is_null($file->deleted_at)) {
        $paths = [];
        $folder = $file->folder;
        if ($folder) {
            $paths[] = \drewlabs_folder_to_path_dto($folder);
        }
        $paths[] = \drewlabs_file_to_path_dto($file, $authorizations, $sharingDetails);
        return $paths;
    }
    return [];
}

function drewlabs_folder_to_path_dto($folder)
{
    if ($folder) {
        $folderPath =  \drewlabs_sys_path_to_app_path(rtrim($folder->fullpath, DIRECTORY_SEPARATOR));
        return new \Drewlabs\Packages\UploadedFile\Models\Dto\PathDataTransfertObject([
            "key" => $folderPath . DIRECTORY_SEPARATOR,
            "relativeKey" => $folderPath . DIRECTORY_SEPARATOR,
            "created" => $folder->created_at,
            "modified" => $folder->updated_at,
            "id" => $folder->id,
            "size" => null,
            "is_directory" => true,
            "extension" => null,
            "url" => null,
            "name" => $folder->basename

        ]);
    }
    return $folder;
}

function drewlabs_file_to_path_dto($file, $authorizations = null, $sharingDetails = [])
{
    if ($file) {
        $path =  \drewlabs_file_sys_path_to_app_path($file->storage_path);
        return new \Drewlabs\Packages\UploadedFile\Models\Dto\PathDataTransfertObject(
            array_merge(
                [
                    "key" => $path,
                    "relativeKey" => $path,
                    "created" => $file->created_at,
                    "modified" => $file->updated_at,
                    "id" => $file->id,
                    "size" => $file->size,
                    "is_directory" => false,
                    "extension" => pathinfo($file->name, PATHINFO_EXTENSION),
                    "url" => $file->url,
                    "authorizations" => $authorizations,
                    "name" => $file->label
                ],
                $sharingDetails
            )
        );
    }
    return $file;
}

/**
 * Generate the full path the file uploaded by a user
 * @param string $filename
 * @param string|null $connectedUserFolder
 * @param string|null $folderPath
 */
function generate_file_storage_path($filename, $connectedUserFolder, $folderPath)
{
    return \join_paths(
        array_merge(isset($connectedUserFolder) ? [$connectedUserFolder] : [], isset($folderPath) ? [$folderPath] : [], [$filename])
    );
}