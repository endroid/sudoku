# Concepts

The concepts of which 

A sudoku puzzle is constituted by a board, which has rows, columns and blocks.
Each row, column or block (called a section) has nine cells which overlap with
other sections.

# Avoiding inconsistent state

A section should always have 9 cells. This means that it should be impossible
to have a state where the section no cells or an other number than 9. We can
achieve this in 2 ways: by passing the cells via the constructor and checking
there or by creating the 9 cells when creating the section. The last option is
already impossible as cells are shared between sections so we need to pass the
cells via the constructor.

# Creating a board

There are different ways to handle board creation: via a factory, via the
constructor or via Board::create methods. Opposite to the sections we do not
have to pass all cells or sections to the constructor. However we do need to
make sure that after construction we have a valid board with valid sections.
As it is possible to instantiate a board without a factory or factory method,
creation of cells and sections should be initiated inside the constructor.

Still we can use factory methods to instantiate a board from a string.

# Sections

The board has to distinguish between rows, columns and blocks. For instance
when a user has a board and wants to set the value of cell X,Y to V the board
should know where to find the X-th row and Y-th column to set the value.

# Responsibilities

An object should always have a minimum number of responsibilities. One of the
questions is: should a cell know its row, column and block and should a section
know its board? To answer this question we first ask the question: does a cell
need its section to exist and does a section need its board to exist.

# Control flow

An action or change is always initiated by the actor, which can be either a user
or the solver. When we create a user interface, the user can act directly on the
cells, so there is no need for passing all values via the board. However we also
want to be able to set the value for cell X,Y directly on the board without the
need to retrieve the cells and find the correct one. This means both the board
and the cell get a setValue call.

The solver can take the state of the board and all set values and perform a
series of moves to help the user. The solver can either reduce options, solve the
complete board or give a hint. Here it is important to mention that it is not
the responsibility of the board to reduce options or solve the complete board.
The board, cells and sections can contain extra information to make solving easier
but the logic and control is responsibility of the actor.

# Solving

The process of solving consists of two parts: first we eliminate all options and
deduce new values. When no more deduction is possible we start guessing values
until we either get a valid board or until we encounter an inconsistency or
conflict.

# Moves

When the user or the solver makes a move, it is possible that the move is invalid
and we need to undo the move. This means that we need some sort of undo function
which resets the old state.

The easiest way to achieve this is making a deep copy of the board as a backup and
restoring it when an error occurs. However, when we look at the solver which might
make a lot of moves or guesses before it concludes incorrectness this can result
in serious memory issues.

In the ideal case, only the changes between the boards are stored and only the
changed information is updated. However, comparing a complete board against another
board consumes a lot of computing resources that we want to use for the solving
process.

A more optimal solution is to keep a history of states where for every change you
make on the board you update the current state. Then when you need to undo the
state you only iterate through the changes.

# Performance



# Setting a value

When we set a cell value this has consequences for the options in adjacent cells