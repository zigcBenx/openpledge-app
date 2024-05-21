export const addWhiteSpaceInCardNumbers = (card) => {
  const formattedCard = card.replace(/\s/g, '');
  let result = '';

  for (let i = 0; i < formattedCard.length; i++) {
    if (i > 0 && i % 4 === 0) {
      result += ' ';
    }
    result += formattedCard[i];
  }

  return result;
};