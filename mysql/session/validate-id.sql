SELECT
    COUNT(`sessionId`) AS `count`
FROM
    `session`
WHERE
    `sessionId`=:sessionId