<?php

namespace MartinCamen\Radarr\Actions;

use MartinCamen\ArrCore\Client\RestClientInterface;
use MartinCamen\Radarr\Data\Enums\MovieEndpoint;
use MartinCamen\Radarr\Data\Responses\Movie;
use MartinCamen\Radarr\Data\Responses\MovieCollection;

/** @link https://radarr.video/docs/api/#/Movie */
final readonly class MovieActions
{
    public function __construct(private RestClientInterface $restClient) {}

    /** @link https://radarr.video/docs/api/#/Movie/get_api_v3_movie */
    public function all(): MovieCollection
    {
        return MovieCollection::fromArray(
            $this->restClient->get(MovieEndpoint::All),
        );
    }

    /** @link https://radarr.video/docs/api/#/Movie/get_api_v3_movie__id_ */
    public function find(int $id): Movie
    {
        return Movie::fromArray(
            $this->restClient->get(MovieEndpoint::ById, ['id' => $id]),
        );
    }

    /**
     * Search for movies by term (title, IMDb ID, or TMDb ID).
     *
     * @link https://radarr.video/docs/api/#/Movie/get_api_v3_movie_lookup
     */
    public function search(string $term): MovieCollection
    {
        return MovieCollection::fromArray(
            $this->restClient->get(MovieEndpoint::Lookup, ['term' => $term]),
        );
    }

    /** @link https://radarr.video/docs/api/#/Movie/get_api_v3_movie_lookup_tmdb */
    public function searchByTmdb(int $tmdbId): Movie
    {
        return Movie::fromArray(
            $this->restClient->get(MovieEndpoint::LookupTmdb, ['tmdbId' => $tmdbId]),
        );
    }

    /** @link https://radarr.video/docs/api/#/Movie/get_api_v3_movie_lookup_imdb */
    public function searchByImdb(string $imdbId): Movie
    {
        return Movie::fromArray(
            $this->restClient->get(MovieEndpoint::LookupImdb, ['imdbId' => $imdbId]),
        );
    }

    /**
     * Add a new movie.
     *
     * @param array<string, mixed> $movieData
     *
     * @link https://radarr.video/docs/api/#/Movie/post_api_v3_movie
     */
    public function add(array $movieData): Movie
    {
        return Movie::fromArray(
            $this->restClient->post(MovieEndpoint::All, $movieData),
        );
    }

    /**
     * Update an existing movie.
     *
     * @param array<string, mixed> $movieData
     *
     * @link https://radarr.video/docs/api/#/Movie/put_api_v3_movie__id_
     */
    public function update(int $id, array $movieData): Movie
    {
        $result = $this->restClient->put(
            MovieEndpoint::ById,
            array_merge(['id' => $id], $movieData),
        );

        return Movie::fromArray($result);
    }

    /**
     * Delete a movie.
     *
     * @link https://radarr.video/docs/api/#/Movie/delete_api_v3_movie__id_
     */
    public function delete(int $id, bool $deleteFiles = false, bool $addImportExclusion = false): void
    {
        $this->restClient->delete(MovieEndpoint::ById, [
            'id'                 => $id,
            'deleteFiles'        => $deleteFiles,
            'addImportExclusion' => $addImportExclusion,
        ]);
    }
}
