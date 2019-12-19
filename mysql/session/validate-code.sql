SELECT
    COUNT(`sessionId`) AS `count`
FROM
    `session`
WHERE
    `sessionCode`=:sessionCode