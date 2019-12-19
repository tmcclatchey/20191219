SELECT
    `qaId`,
    `qaQuestion`,
    `qaHash`,
    `qaAdded`,
    `qaUpdated`,
    `userId`
FROM
    `user_qa`
WHERE
    `userId`=:userId
LIMIT
    :startingIndex, :recordLimit