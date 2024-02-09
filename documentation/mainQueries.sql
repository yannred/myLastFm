-- Query for finding duplicate scrobbles
SELECT
    CONCAT(track_id, timestamp),
    count(CONCAT(track_id, timestamp))
FROM scrobble
GROUP BY CONCAT(track_id, timestamp)
HAVING count(CONCAT(track_id, timestamp)) > 1;

-- Delete all scrobbles
DELETE FROM import WHERE 1;
DELETE FROM scrobble WHERE 1;
DELETE FROM track WHERE 1;
DELETE FROM album WHERE 1;
DELETE FROM artist WHERE 1;

-- count of scrobbles
select count(*) from scrobble;

