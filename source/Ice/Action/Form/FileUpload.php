<?php

namespace Ice\Action;

use FileAPI;
use Ice\Core\Action;
use Ice\DataProvider\Request;
use Ice\Helper\File;

class Form_FileUpload extends Action
{

    /**
     * Action config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'view' => ['template' => '', 'viewRenderClass' => null],
            'actions' => [],
            'input' => [
                'token' => ['providers' => Request::class, 'validators' => 'Ice:Not_Empty'],
                'formName' => ['providers' => Request::class, 'validators' => 'Ice:Not_Empty'],
                'fieldName' => ['providers' => Request::class, 'validators' => 'Ice:Not_Empty'],
            ],
            'output' => [],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'access' => [
                'roles' => [],
                'request' => null,
                'env' => null
            ]
        ];
    }

    /** Run action
     *
     * @param  array $input
     * @return array
     */
    public function run(array $input)
    {
        require_once VENDOR_DIR . 'mailru/fileapi/server/FileAPI.class.php';

        $files = FileAPI::getFiles();

        $data = array();

        // Fetch all file-info from files list
        $this->fetchFiles($files, $data, 'file', $input);

        // JSONP callback name
        $jsonp = isset($_REQUEST['callback']) ? trim($_REQUEST['callback']) : null;

        // JSON-data for server response
        $json = [
            'files' => $data,
            'data' => array('_REQUEST' => $_REQUEST, '_FILES' => $files)
        ];

        // Server response: "HTTP/1.1 200 OK"
        FileAPI::makeResponse(
            [
                'status' => FileAPI::OK,
                'statusText' => 'OK',
                'body' => $json
            ],
            $jsonp
        );
        exit;
    }

    private function fetchFiles($files, &$data, $name, $input)
    {
        if (isset($files['tmp_name'])) {
            if (!$files['size']) {
                $this->getLogger()->error(
                    [
                        'File {$0} is empty (size 0). Check php directives post_max_size and upload_max_filesize',
                        basename($files['name'])
                    ],
                    __FILE__,
                    __LINE__
                );

                return;
            }

            $to = implode(
                '/',
                ['temp', $input['formName'], $input['fieldName'], $input['token'], basename($files['name'])]
            );

            $filename = File::copy($files['tmp_name'], getUploadDir() . $to);
            list($mime) = explode(';', mime_content_type($filename));

            $data[$name] = [
                'mime' => $mime,
                'size' => filesize($filename),
            ];
        } else {
            foreach ($files as $name => $file) {
                $this->fetchFiles($file, $data, $name, $input);
            }
        }
    }
}