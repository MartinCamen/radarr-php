<?php

namespace MartinCamen\Radarr\Actions;

use MartinCamen\ArrCore\Client\RestClientInterface;
use MartinCamen\ArrCore\Data\Responses\Command;
use MartinCamen\Radarr\Data\Enums\CommandEndpoint;
use MartinCamen\Radarr\Data\Enums\CommandName;

/** @link https://radarr.video/docs/api/#/Command */
final readonly class CommandActions
{
    public function __construct(private RestClientInterface $client) {}

    /**
     * Get all commands.
     *
     * @return array<int, Command>
     *
     * @link https://radarr.video/docs/api/#/Command/get_api_v3_command
     */
    public function all(): array
    {
        $result = $this->client->get(CommandEndpoint::All);

        return array_map(
            Command::fromArray(...),
            $result ?? [],
        );
    }

    /**
     * Get command by ID.
     *
     * @link https://radarr.video/docs/api/#/Command/get_api_v3_command__id_
     */
    public function get(int $id): Command
    {
        $result = $this->client->get(CommandEndpoint::ById, ['id' => $id]);

        return Command::fromArray($result);
    }

    /**
     * Execute a command.
     *
     * @param array<string, mixed> $body
     *
     * @link https://radarr.video/docs/api/#/Command/post_api_v3_command
     */
    public function run(CommandName $name, array $body = []): Command
    {
        $result = $this->client->post(CommandEndpoint::All, array_merge(
            ['name' => $name->value],
            $body,
        ));

        return Command::fromArray($result);
    }

    /**
     * Cancel a running command.
     *
     * @link https://radarr.video/docs/api/#/Command/delete_api_v3_command__id_
     */
    public function cancel(int $id): void
    {
        $this->client->delete(CommandEndpoint::ById, ['id' => $id]);
    }

    /** Refresh all movie information and rescan disk. */
    public function refreshAllMovies(): Command
    {
        return $this->run(CommandName::RefreshMovie);
    }

    /**
     * Refresh a specific movie's information and rescan disk.
     *
     * @param array<int, int>|null $movieIds
     */
    public function refreshMovie(?array $movieIds = null): Command
    {
        $body = [];

        if ($movieIds !== null) {
            $body['movieIds'] = $movieIds;
        }

        return $this->run(CommandName::RefreshMovie, $body);
    }

    /** Search for all missing movies. */
    public function searchMissing(): Command
    {
        return $this->run(CommandName::MissingMoviesSearch);
    }

    /** Search for movies below cutoff quality. */
    public function searchCutoffUnmet(): Command
    {
        return $this->run(CommandName::CutoffUnmetMoviesSearch);
    }

    /**
     * Search for specific movies.
     *
     * @param array<int, int> $movieIds
     */
    public function searchMovies(array $movieIds): Command
    {
        return $this->run(CommandName::MoviesSearch, ['movieIds' => $movieIds]);
    }

    /** Trigger RSS sync. */
    public function rssSync(): Command
    {
        return $this->run(CommandName::RssSync);
    }

    /** Create a backup. */
    public function backup(): Command
    {
        return $this->run(CommandName::Backup);
    }

    /**
     * Rename movie files.
     *
     * @param array<int, int> $movieIds
     */
    public function renameMovies(array $movieIds): Command
    {
        return $this->run(CommandName::RenameMovie, ['movieIds' => $movieIds]);
    }

    /**
     * Rename specific files.
     *
     * @param array<int, int> $files
     */
    public function renameFiles(int $movieId, array $files): Command
    {
        return $this->run(CommandName::RenameFiles, [
            'movieId' => $movieId,
            'files'   => $files,
        ]);
    }
}
