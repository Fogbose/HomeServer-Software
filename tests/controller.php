<?php
declare(strict_types=1);
namespace OCP { interface IRequest {} interface INavigationManager { public function getAll($type); } interface IURLGenerator { public function linkToRoute($route); public function getAbsoluteURL($path); public function imagePath($app,$icon); } interface IUserSession { public function getUser(); } }
namespace OCP\App { interface IAppManager { public function isEnabledForUser($id,$user); public function getAppInfo($id); } }
namespace OCP\AppFramework { class Controller { public function __construct($name,$request) {} } }
namespace OCP\AppFramework\Http { class TemplateResponse { public function __construct(public $app,public $template,public $params) {} } }
namespace {
require __DIR__.'/../homehub/lib/Controller/PageController.php';
$user = new \stdClass();
$session = new class($user) implements \OCP\IUserSession { public function __construct(private $user) {} public function getUser(){return $this->user;} };
$apps = new class($user) implements \OCP\App\IAppManager {
    public function __construct(private $user) {}
    public function isEnabledForUser($id,$user){if($user !== $this->user) throw new \RuntimeException('Wrong user'); return in_array($id,['tasks','notes','forms','deck','cookbook'],true);}
    public function getAppInfo($id){return ['navigations'=>['navigation'=>[['route'=>'notes.page.index']]]];}
};
$urls = new class implements \OCP\IURLGenerator {
    public function linkToRoute($route){return '/index.php/apps/notes/';}
    public function getAbsoluteURL($path){return 'https://cloud.example.org'.$path;}
    public function imagePath($app,$icon){return '/apps/'.$app.'/img/'.$icon;}
};
$nav = new class implements \OCP\INavigationManager { public function getAll($type){return [
    ['id'=>'tasks','href'=>'/index.php/apps/tasks/'],
    ['id'=>'forms','href'=>'https://attacker.example/forms'],
    ['id'=>'deck','href'=>'//attacker.example/deck'],
    ['id'=>'cookbook','href'=>'https://cloud.example.org/index.php/apps/cookbook/'],
];} };
$controller = new \OCA\HomeHub\Controller\PageController('homehub',new class implements \OCP\IRequest {},$apps,$urls,$session,$nav);
$result = $controller->index();
$cards = array_column($result->params['cards'],null,'id');
function check($condition,$message){if(!$condition) throw new \RuntimeException($message);}
check(array_keys($cards) === ['cookbook','tasks','notes'],'Availability and unsafe link filtering');
check($cards['tasks']['url'] === '/index.php/apps/tasks/','Canonical navigation');
check($cards['notes']['url'] === '/index.php/apps/notes/','Declared-route fallback');
check($cards['cookbook']['url'] === 'https://cloud.example.org/index.php/apps/cookbook/','Same-origin URL');
check($cards['notes']['icon'] === '/apps/notes/img/notes.svg','Installed icon');
check($result->app === 'homehub' && $result->template === 'index','Template selection');
echo "CONTROLLER_OK: permission filtering, navigation, fallback and URL boundaries\n";
}
