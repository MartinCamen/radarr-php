<?php

namespace MartinCamen\Radarr\Actions;

use MartinCamen\ArrCore\Client\RestClientInterface;
use MartinCamen\Radarr\Data\Enums\MovieEndpoint;
use MartinCamen\Radarr\Data\Responses\Movie;
use MartinCamen\Radarr\Data\Responses\MovieCollection;

/** @link https://radarr.video/docs/api/#/Movie */
final readonly class MovieActions
{
    public function __construct(private RestClientInterface $client) {}

    /** @link https://radarr.video/docs/api/#/Movie/get_api_v3_movie */
    public function all(?int $tmdbId = null): MovieCollection
    {
        $params = [];

        if ($tmdbId !== null) {
            $params['tmdbId'] = $tmdbId;
        }

        $result = $this->client->get(MovieEndpoint::All, $params);

        return MovieCollection::fromArray($result);
    }

    /** @link https://radarr.video/docs/api/#/Movie/get_api_v3_movie__id_ */
    public function get(int $id): Movie
    {
        $result = $this->client->get(MovieEndpoint::ById, ['id' => $id]);

        return Movie::fromArray($result);
    }

    /**
     * Search for movies by term (title, IMDb ID, or TMDb ID).
     *
     * @link https://radarr.video/docs/api/#/Movie/get_api_v3_movie_lookup
     */
    public function lookup(string $term): MovieCollection
    {
        $result = $this->client->get(MovieEndpoint::Lookup, ['term' => $term]);

        return MovieCollection::fromArray($result);
    }

    /** @link https://radarr.video/docs/api/#/Movie/get_api_v3_movie_lookup_tmdb */
    public function lookupByTmdb(int $tmdbId): Movie
    {
        $result = $this->client->get(MovieEndpoint::LookupTmdb, ['tmdbId' => $tmdbId]);

        return Movie::fromArray($result);
    }

    /** @link https://radarr.video/docs/api/#/Movie/get_api_v3_movie_lookup_imdb */
    public function lookupByImdb(string $imdbId): Movie
    {
        $result = $this->client->get(MovieEndpoint::LookupImdb, ['imdbId' => $imdbId]);

        return Movie::fromArray($result);
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
        $result = $this->client->post(MovieEndpoint::All, $movieData);

        return Movie::fromArray($result);
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
        $result = $this->client->put(
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
        $this->client->delete(MovieEndpoint::ById, [
            'id'                 => $id,
            'deleteFiles'        => $deleteFiles,
            'addImportExclusion' => $addImportExclusion,
        ]);
    }
}
