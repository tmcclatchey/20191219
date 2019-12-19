SELECT
    COUNT(`sessionId`) AS `count`
FROM
    `session`
WHERE
    `sessionActivity` >= :maximumAge