\c "mayapur-news"
CREATE SCHEMA main;
CREATE EXTENSION pg_trgm WITH SCHEMA main;
CREATE EXTENSION citext WITH SCHEMA main;
CREATE EXTENSION btree_gin WITH SCHEMA main;

CREATE DATABASE "mayapur-news-logs";
\c "mayapur-news-logs"
CREATE SCHEMA main;
CREATE EXTENSION pg_trgm WITH SCHEMA main;

\c "mayapur-news"
