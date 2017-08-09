#include <math.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#ifndef NULL
#define NULl ((void *)0);
#endif

const char A = 'A';
const char B = 'B';
const char C = 'C';

static int steps = 0;

typedef struct _Step {
    int sn;
    char from;
    char to;
    struct _Step *next;
} Step;

Step *firstStep, *lastStep;

/**
 *
 * Hanoi:
 *
 *      |         |           |
 *      |         |           |
 *      |         |           |
 *     _|_        |           |
 *   _|_|_|_      |           |
 *  |   |   |     |           |
 * --------------------------------
 *      A         B           C
 *
 *
 */
int main() {
    void hanoi(int /*disks*/, char /*from*/, char /*to*/);
    void output();
    int disks;
    printf("How many disks are there: ");
    scanf("%d", &disks);
    hanoi(disks, A, C);
    printf("Steps in total: %d\n", steps);
    output();
}

int length(int num) {
    int len = 0;
    while (num) {
        len++;
        num /= 10;
    }
    return len;
}

char *int_2_string(int num) {
    int len = length(num);
    char str[len + 1], *p;
    int i;
    p = str;
    for (i = 0; i < len; i++) {
        str[i] = num / (int) pow(10, len - i - 1) % 10 + 48;
    }
    str[i] = '\0';

    return p;
}

void output() {
    Step *p = firstStep;
    char format[100] = "%";
//    sprintf(format, "%%%dd: %%c->%%c\n", len);
    strcat(format, int_2_string(length(steps)));
    strcat(format, "d: %c->%c\n");

    do {
        printf(format, p->sn, p->from, p->to);
    } while ((p = p->next) != NULL);
}

void move(char from, char to) {
    Step *p = (Step *) malloc(sizeof(Step));
    steps++;
    if (steps == 1) {
        firstStep = p;
    } else {
        lastStep->next = p;
    }
    p->sn = steps;
    p->from = from;
    p->to = to;
    p->next = NULL;
    lastStep = p;
}

char third(char first, char second) {
    return A + B + C - first - second;
}

void hanoi(int disk_count, char from, char to) {
    char middle;

    if (disk_count == 1) {
        move(from, to);
    } else {
        middle = third(from, to);
        hanoi(disk_count - 1, from, middle);
        move(from, to);
        hanoi(disk_count - 1, middle, to);
    }
}
