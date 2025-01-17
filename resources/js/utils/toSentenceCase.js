/**
 * Converts a string to sentence case (first letter uppercase, rest lowercase)
 * @param {string} str - The string to convert
 * @returns {string} The converted string
 */
export const toSentenceCase = (str) => str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();