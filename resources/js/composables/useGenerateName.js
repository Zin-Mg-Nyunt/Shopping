export const useGenerateName = () => {
    const generateName = (name) => {
        return name
            .trim()
            .toLowerCase()
            .split(' ')
            .map((word) => word[0].toUpperCase() + word.slice(1))
            .join(' ');
    };

    return generateName;
};
