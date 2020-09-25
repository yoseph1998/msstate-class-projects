package finch.robot.framework;

import java.util.ArrayList;

/**
 * Created by yoseph on 5/21/2016.
 * This runs all the commands on a thread separate from the programs to not slow the program if a command is lagging.
 */
public class CommandScheduler implements Runnable {

    private final long rate = 20;
    private volatile boolean running = false;
    private volatile Thread thread;
    private volatile ArrayList<Command> commands;
    private volatile boolean stopped = false;

    public CommandScheduler() {
        running = false;
    }

    /**
     * Starts the Scheduler thread.
     */
    public synchronized void start() {
        if (thread == null) {
            thread = new Thread(this);
            running = true;
            thread.start();
        }
    }

    /**
     * Peacefully ends the CommandScheduler by allowing to finish it's current loop and interrupt all commands.
     */
    public synchronized void stop() {
        running = false;
    }

    /**
     * Initializes command list.
     */
    private void init() {
        commands = new ArrayList<Command>();
    }

    /**
     * Updates and loops through all commands.
     */
    private void update() {
        for (int i = 0; i < commands.size(); i++) {
            if (commands.get(i).hasInterrupted()) {
                commands.get(i)._interrupt();
            }
            if (commands.get(i).hasEnded()) {
                commands.get(i).reset();
                commands.remove(i);
                i--;
                if (i < 0)
                    break;
            }
            commands.get(i)._update();
        }
    }

    /**
     * Interrupts all running commands.
     */
    private void end() {
        for (int i = 0; i < 0; i++) {
            commands.get(i).stop();
        }
    }

    /**
     * Adds the given command to the scheduler.
     *
     * @param command The command desired to be added.
     */
    public synchronized void addCommand(Command command) {
        commands.add(command);
    }

    /**
     * @param command The command to remove.
     * @Deprecated This should not be used because it does not allow the command to end peacefully. The stop method
     * for the chosen command should be used instead.
     * Removes a command from the array of currently running commands.
     */
    @Deprecated
    public synchronized void removeCommand(Command command) {
        for (int i = 0; i < commands.size(); i++)
            if (commands.get(i).equals(command)) {
                commands.remove(i);
                return;
            }
    }

    /**
     * This will allow the thread to run at a constant rate and also hold the scheduler loop.
     */
    @Override
    public void run() {
        init();
        long begin = System.nanoTime();
        long time = 0;
        long wait = 0;
        while (running) {
            time = System.nanoTime();

            wait = rate - (time - begin);

            if (wait < 0)
                wait = 5;

            try {
                thread.wait(wait);
            } catch (Exception e) {

            }
            update();
        }
        end();
        stopped = true;
    }

    /**
     * @return If the thread has stopped.
     */
    public boolean isStopped() {
        return stopped;
    }

    public void processInput(String[] input) {
        if (input == null)
            return;
        if (input.length == 2)
            if (input[0].equals("stop"))
                if (commands.size() >= 1)
                    if (input[1].equals("commands"))
                        for (int i = 0; i < commands.size(); i++)
                            commands.get(i).stop();
                    else if (input[1].equals("command"))
                        commands.get(commands.size() - 1).stop();
    }
}
