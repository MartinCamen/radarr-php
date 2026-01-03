<?php

namespace MartinCamen\Radarr\Actions;

use MartinCamen\ArrCore\Actions\CommandActions as CoreCommandActions;
use MartinCamen\ArrCore\Data\Enums\CommandName;
use MartinCamen\ArrCore\Data\Responses\Command;

/** @link https://radarr.video/docs/api/#/Command */
final readonly class CommandActions extends CoreCommandActions
{
    /**
     * Refresh all/specific movie information and rescan disk.
     *
     * @param array<int, int> $ids
     */
    public function refresh(array $ids = []): Command
    {
        $body = [];

        if ($ids !== []) {
            $body['movieIds'] = $ids;
        }

        return $this->run(CommandName::RefreshMovie, $body);
    }

    /** Search for all missing movies. */
    public function missing(): Command
    {
        return $this->run(CommandName::MissingMoviesSearch);
    }

    /** Search for movies below cutoff quality. */
    public function cutoffUnmet(): Command
    {
        return $this->run(CommandName::CutoffUnmetMoviesSearch);
    }

    /**
     * Search for specific movies.
     *
     * @param array<int, int> $ids
     */
    public function searchMovies(array $ids): Command
    {
        return $this->run(CommandName::MoviesSearch, ['movieIds' => $ids]);
    }

    /**
     * Rename movie files.
     *
     * @param array<int, int> $ids
     */
    public function renameMovies(array $ids): Command
    {
        return $this->run(CommandName::RenameMovie, ['movieIds' => $ids]);
    }

    /**
     * Rename specific files.
     *
     * @param array<int, int> $files
     */
    public function renameFiles(int $id, array $files): Command
    {
        return $this->run(CommandName::RenameFiles, [
            'movieId' => $id,
            'files'   => $files,
        ]);
    }
}
