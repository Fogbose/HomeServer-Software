<?php
declare(strict_types=1);
namespace OCA\HomeHub\Controller;

use OCP\App\IAppManager;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\Attribute\NoCSRFRequired;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCP\INavigationManager;
use OCP\IURLGenerator;
use OCP\IUserSession;

class PageController extends Controller {
    public function __construct(string $appName, IRequest $request,
        private IAppManager $apps, private IURLGenerator $urls, private IUserSession $session,
        private INavigationManager $navigation) {
        parent::__construct($appName, $request);
    }

    #[NoAdminRequired]
    #[NoCSRFRequired]
    public function index(): TemplateResponse {
        $definitions = [
            ['spreed', 'Messagerie', 'Discuter, appeler et partager ensemble.', 'Quotidien', '#2770a4', 'app.svg'],
            ['cookbook', 'Recettes', 'Vos recettes, à portée de main.', 'Quotidien', '#ac5029', 'app.svg'],
            ['cospend', 'Dépenses partagées', 'Les comptes du foyer, simplement.', 'Quotidien', '#247562', 'app.svg'],
            ['tasks', 'Tâches', 'Ce qui est à faire, aujourd’hui et après.', 'Organisation', '#5867b0', 'tasks.svg'],
            ['calendar', 'Agenda', 'Rendez-vous et moments à partager.', 'Organisation', '#326f9d', 'calendar.svg'],
            ['contacts', 'Contacts', 'Retrouver les bonnes personnes.', 'Organisation', '#7e61a1', 'app.svg'],
            ['notes', 'Notes', 'Garder une idée, une liste, un mémo.', 'Documents', '#a07826', 'notes.svg'],
            ['passwords', 'Mots de passe', 'Votre coffre de mots de passe.', 'Documents', '#536775', 'app.svg'],
            ['memories', 'Photos & vidéos', 'Revenir sur vos moments préférés.', 'Quotidien', '#a15375', 'app.svg'],
            ['tables', 'Tableaux', 'Organiser les informations utiles.', 'Documents', '#397d82', 'app.svg'],
            ['forms', 'Formulaires', 'Poser des questions et recueillir les réponses.', 'Organisation', '#8d609e', 'forms.svg'],
            ['deck', 'Projets', 'Faire avancer vos projets ensemble.', 'Organisation', '#587c51', 'deck.svg'],
        ];
        $cards = [];
        $user = $this->session->getUser();
        $entries = $this->navigation->getAll('link');
        foreach ($definitions as [$id, $name, $description, $category, $color, $icon]) {
            if (!$this->apps->isEnabledForUser($id, $user)) {
                continue;
            }
            $href = null;
            foreach ($entries as $entry) {
                if (($entry['id'] ?? '') === $id) {
                    $href = $entry['href'];
                    break;
                }
            }
            // App navigation can lag immediately after an enable operation.
            // Resolve the declared route as a fallback, never assemble a URL.
            if ($href === null) {
                $info = $this->apps->getAppInfo($id);
                foreach ($info['navigations']['navigation'] ?? [] as $navigation) {
                    if (!empty($navigation['route'])) {
                        $href = $this->urls->linkToRoute($navigation['route']);
                        break;
                    }
                }
            }
            if (!is_string($href) || !(str_starts_with($href, '/') && !str_starts_with($href, '//')
                || str_starts_with($href, $this->urls->getAbsoluteURL('/')))) {
                continue;
            }
            $cards[] = ['id' => $id, 'name' => $name, 'description' => $description,
                'category' => $category, 'color' => $color,
                'url' => $href,
                'icon' => $this->urls->imagePath($id, $icon)];
        }
        return new TemplateResponse('homehub', 'index', ['cards' => $cards]);
    }
}
