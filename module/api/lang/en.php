<?php
/**
 * The api module English file of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     api
 * @version     $Id: English.php 824 2010-05-02 15:32:06Z wwccss $
 * @link        http://www.zentao.net
 */
$lang->api = new stdclass();
$lang->api->common   = 'API';
$lang->api->getModel = 'Super Model API';
$lang->api->sql      = 'SQL Query API';

$lang->api->index            = 'Api Doc Home';
$lang->api->editLib          = 'Edit Api Doc';
$lang->api->releases         = 'Releases';
$lang->api->deleteRelease    = 'Delete Release';
$lang->api->deleteLib        = 'Delete Api Doc';
$lang->api->createRelease    = 'Publish';
$lang->api->createLib        = 'Create Api Library';
$lang->api->createApi        = 'Create Api';
$lang->api->createAB         = 'Create';
$lang->api->edit             = 'Edit';
$lang->api->delete           = 'Delete';
$lang->api->position         = 'Positions';
$lang->api->startLine        = "%s,%s";
$lang->api->desc             = 'Description';
$lang->api->debug            = 'Debug';
$lang->api->submit           = 'Submit';
$lang->api->url              = 'Url';
$lang->api->result           = 'Result';
$lang->api->status           = 'Status';
$lang->api->data             = 'Content';
$lang->api->noParam          = 'Get debugging does not require input parameters，';
$lang->api->noModule         = 'There is no directory under the interface library. Please maintain the directory first';
$lang->api->post             = 'Please refer to the page form for post debugging';
$lang->api->noUniqueName     = 'Api library name already exists。';
$lang->api->noUniqueVersion  = 'Version already exists。';
$lang->api->version          = 'Version';
$lang->api->createStruct     = 'Create Data Structure';
$lang->api->editStruct       = 'Edit Data Structure';
$lang->api->deleteStruct     = 'Delete Data Structure';
$lang->api->create           = 'Ceate Doc';
$lang->api->title            = 'Interface Name';
$lang->api->pageTitle        = 'Api Library';
$lang->api->module           = 'Directory';
$lang->api->apiDoc           = 'Interface';
$lang->api->manageType       = 'Manage Directory';
$lang->api->managePublish    = 'Manage Version';
$lang->api->doing            = 'Doing';
$lang->api->done             = 'Done';
$lang->api->basicInfo        = 'Essential Information';
$lang->api->apiDesc          = 'Interface Description';
$lang->api->confirmDelete    = "Are you sure to delete this interface？";
$lang->api->confirmDeleteLib = "Are you sure to delete this interface library？";
$lang->api->filterStruct     = "use struct";
$lang->api->defaultVersion   = "Current Version";

/* Common access control lang. */
$lang->api->whiteList          = 'White list';
$lang->api->aclList['open']    = 'Open';
$lang->api->aclList['private'] = 'Private';
$lang->api->aclList['custom']  = 'Custom';
$lang->api->group              = 'Group';
$lang->api->user               = 'User';

$lang->api->noticeAcl = array(
    'open'    => 'Users who can access the api library which the library belongs can access it.',
    'custom'  => 'Users in the whiltelist can access it.',
    'private' => 'Only the one who created it can access it.',
);

/* fields of struct */
$lang->struct = new stdClass();

$lang->struct->add             = 'Add';
$lang->struct->field           = 'Field';
$lang->struct->paramsType      = 'Type';
$lang->struct->required        = 'Require';
$lang->struct->desc            = 'Description';
$lang->struct->descPlaceholder = 'Parameter description';
$lang->struct->action          = 'Action';
$lang->struct->addSubField     = 'Add Subfield';

$lang->struct->typeOptions = array(
    'formData' => 'FormData',
    'json'     => 'JSON',
    'array'    => 'Array',
    'object'   => 'Object',
);

/* fields of form */
$lang->api->struct             = 'Data Structure';
$lang->api->structName         = 'Structure Name';
$lang->api->structType         = 'Type';
$lang->api->structAttr         = 'Attribute';
$lang->api->structAddedBy      = 'Creator';
$lang->api->structAddedDate    = 'Created Time';
$lang->api->name               = 'Interface Library Name';
$lang->api->baseUrl            = 'Base Url';
$lang->api->baseUrlDesc        = 'Site or path. for example, api.zentao.com or /v1';
$lang->api->desc               = 'Description';
$lang->api->control            = 'Access Control';
$lang->api->noLib              = 'There is no interface library at present。';
$lang->api->noApi              = 'There is no interface for the time being。';
$lang->api->noStruct           = 'There is no structure for the time being。';
$lang->api->lib                = 'Interface Library';
$lang->api->apiList            = 'Interface List';
$lang->api->formTitle          = 'Interface Name';
$lang->api->path               = 'Request Path';
$lang->api->protocol           = 'Request Protocol';
$lang->api->method             = 'Request Method';
$lang->api->requestType        = 'Request Type';
$lang->api->status             = 'Development Status';
$lang->api->owner              = 'Person In Charge';
$lang->api->paramsExample      = 'Response Example';
$lang->api->header             = 'Request Header';
$lang->api->query              = 'Request Parameters';
$lang->api->params             = 'Request Body';
$lang->api->response           = 'Response';
$lang->api->responseExample    = 'Response Example';
$lang->api->res                = new stdClass();
$lang->api->res->name          = '名称';
$lang->api->res->desc          = 'Description';
$lang->api->res->type          = 'Type';
$lang->api->req                = new stdClass();
$lang->api->req->name          = 'Name';
$lang->api->req->desc          = 'Description';
$lang->api->req->type          = 'Type';
$lang->api->req->required      = 'Required';
$lang->api->field              = 'Field';
$lang->api->scope              = 'Position';
$lang->api->paramsType         = 'Type';
$lang->api->required           = 'Required';
$lang->api->default            = 'Default';
$lang->api->desc               = 'Description';
$lang->api->customType         = 'Custom Structure';
$lang->api->format             = 'Format';
$lang->api->methodOptions      = array(
    'GET'     => 'GET',
    'POST'    => 'POST',
    'PUT'     => 'PUT',
    'DELETE'  => 'DELETE',
    'PATCH'   => 'PATCH',
    'OPTIONS' => 'OPTIONS',
    'HEAD'    => 'HEAD'
);
$lang->api->protocalOptions    = array(
    'HTTP'  => 'HTTP',
    'HTTPS' => 'HTTPS',
);
$lang->api->requestTypeOptions = array(
    'application/json'                  => 'application/json',
    'application/x-www-form-urlencoded' => 'application/x-www-form-urlencoded',
    'multipart/form-data'               => 'multipart/form-data'
);
$lang->api->statusOptions      = array(
    'done'   => 'Done',
    'doing'  => 'Doing',
    'hidden' => 'Hidden'
);
$lang->api->paramsScopeOptions = array(
    'formData' => 'formData',
    'path'     => 'path',
    'query'    => 'query',
    'body'     => 'body',
    'header'   => 'header',
    'cookie'   => 'cookie',
);
/* Api global common params */
$lang->api->paramsTypeOptions = array(
    'object'   => 'object',
    'string'   => 'string',
    'date'     => 'date',
    'datetime' => 'datetime',
    'boolean'  => 'boolean',
    'int'      => 'int',
    'long'     => 'long',
    'float'    => 'float',
    'double'   => 'double',
    'decimal'  => 'decimal'
);

/* Api params */
$lang->api->paramsTypeCustomOptions = array(
    'file' => 'file',
    'ref'  => 'ref',
);

$lang->api->structParamsOptons   = array_merge($lang->api->paramsTypeOptions, array('file' => 'file', 'ref' => 'ref'));
$lang->api->allParamsTypeOptions = array_merge($lang->api->paramsTypeOptions, $lang->api->paramsTypeCustomOptions);
$lang->api->requiredOptions      = array(0 => 'No', 1 => 'Yes');

$lang->doclib       = new stdclass();
$lang->doclib->name = 'Interface Library Name';

$lang->apistruct = new stdClass();
$lang->apistruct->name = 'Structure Name';

$lang->api_lib_release = new stdClass();
$lang->api_lib_release->version = 'Version';

$lang->api->error = new stdclass();
$lang->api->error->onlySelect = 'SQL interface only allow SELECT query.';
$lang->api->error->disabled   = 'For security reasons, this feature is disabled. You can go to the config directory and modify the configuration item %s to open this function.';
