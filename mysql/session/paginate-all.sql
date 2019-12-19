SELECT
    `sessionId`,
    `sessionCode`,
    `sessionFingerprint`,
    `sessionCreated`,
    `sessionActivity`,
    `sessionData`
FROM
    `session`
LIMIT
    :startingIndex, :recordLimit