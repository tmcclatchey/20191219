SELECT
    `sessionId`,
    `sessionCode`,
    `sessionFingerprint`,
    `sessionCreated`,
    `sessionActivity`,
    `sessionData`
FROM
    `session`
WHERE
    `sessionActivity` < :maximumAge
LIMIT
    :startingIndex, :recordLimit