SELECT
    COUNT(`qaId`) AS `count`
FROM
    `user_qa`
WHERE
    `qaId`=:qaId