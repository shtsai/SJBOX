## SJBOX - Music Streaming Service

**General Design**

For this project, we designed a music streaming service using relational databases.

We have a User table, which contains user’s information including unique uname(username), name,
email, city, and password.

We have a table for Artist, that contains artist id, artist name, and a short description. Each artist can
perform multiple tracks. Each track has a tid, title, and duration. Each track can only be performed by one
artist. Given the dataset provided, we decide to add a constraint that each track must be included in one
album. And as a result, we remove the AlbumSong table from our previous design.

Each user can create playlists, that has a playlist id, date, status (public/private), and title. A playlist can
include tracks from different artist.

We record information of each play of tracks by the users in the Play table. We keep track of the play
time, and whether it is played from a playlist. When it is not played a playlist, we simply leave this
information as NULL.

Users can also gives a score between 0 to 10 for each track. Users can like artists, and follow other users.
We store the timestamps of time when these actions taken place.

**Functionality:**
- Search tracks, albums, artists, or users
- Access album info (including album title, artists, and release date), and all tracks in that album
- Access track info (including artist, album, and duration) and play the track
- Rate a track and access its average rating
- Access artist info (including title, and description), all his/her albums and top tracks
- Suggest similar artists based on the number of common fans (at least 3)
- Like/unlike an artist
- Follow/unfollow an user
- Display a user’s profile, artists he/she likes, users he/she follows, and his/her playlists
- Create playlists, which can be either public or private
- Add/remove tracks into/from an playlist
- Display a customized user feed, including new tracks by artists he/she liked, playlists by users he/she follows (ordered by number of plays), top 10 tracks by average user ratings, and top 10 tracks by number of plays

**Database Schema**

This is the ER diagram of our design.

![alt text](https://github.com/shtsai7/SJBOX/blob/master/images/ERdiagram.png "ER Diagram")

Below are the database schema that we used to create tables.

Note that we remove AlbumSong table from our previous design. We also extends the attribute names.

Here is the database schema we created:
```
User(Username, Name, Email, City, Password)
Artist(ArtistId, ArtistTitle, ArtistDescription)
Track(TrackId, TrackName, TrackDuration, TrackArtist, TrackAlbum)
Album(AlbumId, AlbumName, AlbumReleaseDate)
Playlist(PlaylistId, Username, PlaylistTitle, PlaylistDate, PlaylistStatus)
Follow(Username1, Username2, FollowDate)
Likes(ArtistId, Username, LikeDate)
Rate(Username, TrackId, Score, RateTime)
Play(Username, TrackId, PlayTime, PlaylistId)
PlaylistSong(PlaylistId, TrackId)

Foreign key constraints:
Track.TrackArtist is a foreign key referencing Artist.ArtistTitle.
Track.AlbumId is a foreign key referencing Album.AlbumId.
Playlist.Username is a foreign key referencing User.Username.
Follow.Username1 is a foreign key referencing User.Username.
Follow.Username2 is a foreign key referencing User.Username.
Likes.ArtistId is a foreign key referencing Artist.ArtistId.
Likes.Username is a foreign key referencing User.Username.
Rate.Username is a foreign key referencing User.Username.
Rate.TrackId is a foreign key referencing Track.TrackId.
Play.Username is a foreign key referencing User.Username.
Play.TrackId is a foreign key referencing Track.TrackId.
Play.PlaylistId is a foreign key referencing Playlist.PlaylistId.
PlaylistSong.PlaylistId is a foreign key referencing Playlist.PlaylistId.
PlaylistSong.TrackId is a foreign key referencing Track.TrackId.
```
Assumptions:

1. An artist can have multiple tracks, and a track can only belong to one artist.
2. A track must belongs to an album.
3. A user can only follow the same user, like the same artist, or rate the same track at most once.
4. In Follow table, u1 follows u2.
