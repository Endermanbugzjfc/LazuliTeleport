<?php

declare(strict_types=1);

namespace Endermanbugzjfc\LazuliTeleport\Player;

use Endermanbugzjfc\LazuliTeleport\Commands\Tpablock\BlockSubcommand;
use Endermanbugzjfc\LazuliTeleport\Commands\Tpablock\UnblockSubcommand;
use Endermanbugzjfc\LazuliTeleport\Commands\TpaCommand;
use Endermanbugzjfc\LazuliTeleport\Commands\TpahereCommand;
use Endermanbugzjfc\LazuliTeleport\LazuliTeleport;
use Endermanbugzjfc\LazuliTeleport\Utils\Utils;
use Generator;
use pocketmine\player\Player;
use SOFe\AwaitGenerator\Await;
use SOFe\InfoAPI\AnonInfo;
use SOFe\InfoAPI\InfoAPI;
use SOFe\InfoAPI\NumberInfo;
use SOFe\InfoAPI\StringInfo;
use Vecnavium\FormsUI\CustomForm;
use function array_diff;
use function array_filter;
use function array_merge;
use function array_slice;
use function array_unique;
use function count;
use function explode;
use function implode;
use function in_array;
use function sort;
use function stripos;

/**
 * @internal Use {@link PlayerSession::openPlayerFinder()}.
 */
class PlayerFinder
{
    /**
     * @param PlayerFinderActionInterface[] $actions
     * @return PlayerFinderActionInterface[]
     */
    private static function noBuiltInActions(
        array $actions
    ) : array {
        return array_filter(
            $actions,
            fn(PlayerFinderActionInterface $action) : bool => match ($action::class) {
                TpaCommand::class => false,
                TpahereCommand::class => false,
                BlockSubcommand::class => true,
                UnblockSubcommand::class => true,
                default => true
            }
        );
    }

    /**
     * @return Generator<mixed, mixed, mixed, PlayerFinderActionInterface[]>
     */
    private static function getBuiltInActions() : Generator
    {
        return [
            yield from TpaCommand::getInstance(),
            yield from TpahereCommand::getInstance(),
            yield from BlockSubcommand::getInstance(),
            yield from UnblockSubcommand::getInstance()
        ];
    }

    /**
     * @param string[] &$names
     */
    protected static function playerFinderSorter(
        array &$names
    ) : void {
        sort($names, SORT_STRING);
    }
    /**
     * @param callable(string &$names) : void $filter
     * @param string $search Empty (empty string) = all players will be listed.
     * @param string[] $selections
     * @param PlayerFinderActionInterface[] $actions
     */
    public static function open(
        PlayerSession $session,
        ?PlayerFinderActionInterface $action,
        string $search,
        array $selections,
        ?callable $filter,
        ?array $actions,
    ) : void {
        /**
         * @var callable(): Generator
         */
        $coroutine = function () use (
            $session,
            $actions,
            $action,
            $search,
            $selections,
            $filter
        ) : Generator {
            $searchBar = "searchBar";
            $resultEntry = "resultEntry";
            $actionSelector = "actionSelector";
            $forceMode = "forceMode";
            $waitDuration = "waitDuration";
            $noTarget = false;
            $pluginName = LazuliTeleport::getInstance()->getName();
            $player = $session->getPlayer();
            $messages = $session->getMessages();
            $info = $session->getInfo();
            $infoNamespace = "$pluginName.PlayerFinder";
            $names = LazuliTeleport::getInstance()->getAllPlayerNames();
            $oldForceMode = $session->getForceMode();
            $oldWaitduration = $session->getForceModeWaitDuration();
            $config = LazuliTeleport::getInstance()->getConfigObject();
            $waitDurationMin = $config->waitDurationSliderMin;
            $waitDurationStep = $config->waitDurationSliderStep;
            $waitDurationSteps = $config->waitDurationSliderTotalSteps;

            $title = $messages->playerFinderTitle ?? "";
            $title = InfoAPI::resolve($title, $info);
            $err = $messages->playerFinderNoTargetsSelected;
            $label = $messages->playerFinderLabel ?? "";
            $label = InfoAPI::resolve($label, $info);
            $placeholder = $messages->playerFinderPlaceholder ?? "";
            $placeholder = InfoAPI::resolve($placeholder, $info);
            $resultHeader = $messages->playerFinderSearchResultHeader;
            $entry = $messages->playerFinderSearchResultEntry ?? "";
            $entry = $messages->playerFinderSearchResultEntry ?? "";
            $actionSelectorName = $messages->playerFinderActionSelectorLabel ?? "";
            $actionSelectorName = InfoAPI::resolve($placeholder, $info);
            $forceModeName = $messages->forceModeToggleLabel ?? "";
            $forceModeName = InfoAPI::resolve($forceModeName, $info);
            $waitDurationName = $messages->forceModeWaitDurationSliderLabel ?? "";
            $waitDurationName = InfoAPI::resolve($waitDurationName, $info);

            /**
             * I use loops rather than recursive function calls for user-controlled stuff (a form in this case).
             * Player can just put your server in hell by unstoppably pressing the submit button and cause segmentation fault (core dump) due to stack overflow.
             */
            while (true) {
                $keywords = explode(" ", $search);
                $keywords = array_unique($keywords);
                // Being a gopher here. Using class as data structure.
                $foundClass = (new class() {
                    public string $name;
                    public string $resolved;
                })::class;
                $found = [];
                foreach ($names as $name) {
                    foreach ($keywords as $keyword) {
                        $stripos = stripos($name, $keyword);
                        if ($stripos === false) {
                            continue;
                        }
                        $found[] = $class = new $foundClass();
                        $class->name = $name;
                    }
                }
                if ($filter !== null) {
                    $filter($found);
                }
                static::playerFinderSorter($found);
                $resultCount = count($found);
                $searchContext = new class(
                    $infoNamespace,
                    [
                        "ResultCount" => new NumberInfo($resultCount),
                        "SearchInput" => new StringInfo($search),
                    ],
                    [
                        $info
                    ]
                ) extends AnonInfo {
                };
                foreach ($found as $foundInstance) {
                    $name = $foundInstance->name;
                    $entryInfo = new class(
                        $infoNamespace,
                        [
                            "ResultPlayer" => $name
                        ],
                        [
                            $searchContext
                        ]
                    ) extends AnonInfo {
                    };
                    $resolved = InfoAPI::resolve($entry, $entryInfo);
                    $foundInstance->resolved = $resolved;
                }
                if ($err !== null) {
                    $err = InfoAPI::resolve($err, $searchContext);
                }
                if ($resultHeader !== null) {
                    $resultHeader = InfoAPI::resolve($resultHeader, $searchContext);
                }
                $canForceMode = yield from $session->hasForceModePermission();

                $availableActions = null;
                if ($selections !== []) {
                    $server = $session->getPlayer()->getServer();
                    $canOffline = yield from $session->hasOfflineRequestPermission();
                    $selectedOffline = false;
                    if (!$canOffline) {
                        foreach ($selections as $selection) {
                            $target = $server->getPlayerExact($selection);
                            if ($target === null) {
                                $selectedOffline = true;
                                break;
                            }
                        }
                    }
                    $actions ??= yield from self::getBuiltInActions();
                    $availableActions = [];
                    foreach ($actions as $actionInstance) {
                        $actionAvailable = yield from $actionInstance->isActionAvailable($session, $selections);
                        if ($actionAvailable) {
                            $availableActions[] = $actionInstance;
                        }
                    }
                    $noBuiltInActions = self::noBuiltInActions($availableActions);

                    $tpa = yield from TpaCommand::getInstance();
                    $tpahere = yield from TpahereCommand::getInstance();
                    $block = yield from BlockSubcommand::getInstance();
                    $unblock = yield from UnblockSubcommand::getInstance();
                    $selectionsCount = count($selections);
                    $blockOrUnblock = (yield from $session->isNameBlocked($selections[0]))
                        ? $unblock
                        : $block;
                    $availableActions = match (true) {
                        $selectedOffline => [
                            $blockOrUnblock
                        ], // One selection, includes offline player (triggers only if player does not have the permission make offline requests).
                        $selectionsCount === 1 => [
                            $tpahere,
                            $tpa,
                            $blockOrUnblock
                        ],
                        default => [
                            $block,
                            $tpahere,
                            $unblock,
                        ]
                    };
                    $availableActions = array_merge(
                        $availableActions,
                        $noBuiltInActions
                    );
                }
                /**
                 * @var array{Player, array<int|string, scalar>|null}
                 */
                $formResult = yield from Await::promise(function ($then) use (
                    // Arguments:
                    $session,
                    $search,
                    $noTarget,
                    $selections,
                    $action,

                    // Caches:
                    $found,
                    $player,
                    $availableActions,
                    $oldForceMode,
                    $oldWaitduration,
                    $waitDurationMin,
                    $waitDurationStep,
                    $waitDurationSteps,

                    // Async:
                    $canForceMode,

                    // Message caches:
                    $title,
                    $err,
                    $label,
                    $placeholder,
                    $resultHeader,
                    $actionSelectorName,
                    $forceModeName,
                    $waitDurationName,

                    // Element labels:
                    $searchBar,
                    $resultEntry,
                    $actionSelector,
                    $forceMode,
                    $waitDuration
                ) : void {
                    $form = new CustomForm($then);
                    $form->setTitle($title);
                    if (
                        $noTarget
                        and
                        $err !== null
                    ) {
                        $form->addLabel($err);
                    }
                    $form->addInput($label, $placeholder, $search, $searchBar);
                    if ($resultHeader !== null) {
                        $form->addLabel($resultHeader);
                    }
                    foreach ($found as $foundInstance) {
                        $name = $foundInstance->name;
                        $resolved = $foundInstance->resolved;
                        if (in_array($name, $selections, true)) {
                            $selected = true;
                        } else {
                            $selected = $search === $name;
                        }
                        $form->addToggle($resolved, $selected, "$resultEntry.$name");
                    }
                    if ($availableActions !== null) {
                        $actionIndex = null;
                        foreach ($availableActions as $index => $availableAction) {
                            $actionNames[] = $availableAction->getActionDisplayName($session);
                            if (
                                $action !== null
                                and
                                $availableAction instanceof $action
                            ) {
                                $actionIndex = (int)$index;
                            }
                        }
                        $sliderActionNames = [];
                        foreach ($actionNames as $index => $actionName) {
                            $left = array_slice($actionNames, 0, $index);
                            $right = array_slice($actionNames, $index + 1);

                            $leftList = implode(" ", $left);
                            $rightList = implode(" ", $right);

                            $sliderActionNames[] = "{DarkGray}$leftList {Gold}$actionName {DarkGray}$rightList";
                        }
                        $middleIndex = Utils::getArrayMiddleIndex($sliderActionNames);
                        $form->addStepSlider($actionSelectorName, $sliderActionNames, $actionIndex ?? $middleIndex, $actionSelector);
                        if ($canForceMode) {
                            $form->addToggle($forceModeName, $oldForceMode, $forceMode);
                            $form->addSlider($waitDurationName, $waitDurationMin, $waitDurationMin + $waitDurationStep * $waitDurationSteps, $waitDurationStep, $oldWaitduration, $waitDuration);
                        }
                    }

                    $player->sendForm($form);
                });
                [, $data] = $formResult;
                if ($data === null) {
                    continue;
                }
                $newSearch = (string)$data[$searchBar];
                if ($search !== $newSearch) {
                    $search = $newSearch;
                    continue;
                }
                $newSelections = [];
                foreach ($data as $k => $v) {
                    $explode = explode(".", (string)$k);
                    if ($explode[0] !== $resultEntry) {
                        continue;
                    }
                    if (!$v) {
                        continue;
                    }
                    $newSelections[] = $explode[1] ?? "";
                }
                if (array_diff($selections, $newSelections) !== []) {
                    $selections = $newSelections;
                    continue;
                }
                if ($selections === []) {
                    $noTarget = true;
                    continue;
                }

                $newForceMode = $data[$forceMode] ?? null;
                if ($newForceMode !== null) {
                    $session->setForceMode((bool)$newForceMode);
                }
                $newWaitDuration = $data[$waitDuration] ?? null;
                if ($newWaitDuration !== null) {
                    $session->setForceModeWaitDuration((int)$newWaitDuration);
                }

                $newActionIndex = $data[$actionSelector] ?? null;
                if ($newActionIndex !== null) {
                    $newAction = $actions[(int)$newActionIndex] ?? $action;
                    if ($newAction === null) {
                        continue;
                    }
                    $newAction->runWithSelectedTargets($session, $selections);
                    break;
                }
            }
        };
        Await::f2c($coroutine);
    }
}